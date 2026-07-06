<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class SeoContent
{
    public static function site($key = null, $default = null)
    {
        $site = config('seo_content.site', []);

        return $key === null ? $site : Arr::get($site, $key, $default);
    }

    public static function articles()
    {
        return collect(config('seo_content.articles', []));
    }

    public static function article($slug)
    {
        return self::articles()->firstWhere('slug', $slug);
    }

    public static function faq($key = 'door_faq')
    {
        return config("seo_content.{$key}", []);
    }

    public static function productExtra()
    {
        return config('seo_content.product_extra', []);
    }

    public static function decodedList($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    public static function productFaq(Product $product)
    {
        return self::decodedList($product->faq) ?: self::faq('door_faq');
    }

    public static function articleFaq(Article $article)
    {
        return self::decodedList($article->faq);
    }

    public static function productExtraFor(Product $product)
    {
        $extra = self::decodedList($product->extra_fields);

        return array_replace_recursive(self::productExtra(), $extra);
    }

    public static function titleFor($model, $fallback)
    {
        return !empty($model->seo_title) ? $model->seo_title : $fallback;
    }

    public static function descriptionFor($model, $fallback = null)
    {
        return !empty($model->seo_description) ? $model->seo_description : ($fallback ?: $model->description);
    }

    public static function canonicalFor($model, $path)
    {
        return !empty($model->canonical_url) ? $model->canonical_url : self::canonical($path);
    }

    public static function ogImageFor($model, $fallback)
    {
        return self::absoluteUrl(!empty($model->og_image) ? $model->og_image : $fallback);
    }

    public static function absoluteUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }

        return self::canonical($url);
    }

    public static function canonical($path = null)
    {
        if ($path && preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        $base = rtrim(self::site('domain'), '/');
        $path = $path ?: request()->getPathInfo();

        return $base . '/' . ltrim($path, '/');
    }

    public static function organizationSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => self::site('name'),
            'url' => self::site('domain'),
            'logo' => self::canonical(self::site('logo')),
            'description' => self::site('description'),
        ];
    }

    public static function websiteSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => self::site('name'),
            'url' => self::site('domain'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => self::canonical('/catalog') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function localBusinessSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => self::site('name'),
            'url' => self::site('domain'),
            'image' => self::canonical(self::site('default_image')),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Луцьк',
                'addressCountry' => 'UA',
                'streetAddress' => self::site('address'),
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => self::site('phone'),
                'email' => self::site('email'),
                'contactType' => 'customer service',
                'areaServed' => 'UA',
                'availableLanguage' => ['uk'],
            ],
        ];
    }

    public static function defaultPageSchemas(array $schema = [])
    {
        $schema = array_values(array_filter($schema));
        $topLevelTypes = collect($schema)->pluck('@type')->filter()->all();
        $defaults = [
            self::organizationSchema(),
            self::websiteSchema(),
            self::localBusinessSchema(),
        ];

        foreach ($defaults as $defaultSchema) {
            if (!in_array($defaultSchema['@type'], $topLevelTypes, true)) {
                $schema[] = $defaultSchema;
            }
        }

        return $schema;
    }

    public static function breadcrumbSchema(array $breadcrumbs)
    {
        $items = [];
        $position = 1;

        foreach ($breadcrumbs as $url => $name) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => strip_tags($name),
                'item' => preg_match('/^https?:\/\//', $url) ? $url : self::canonical($url),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    public static function faqSchema(array $faq)
    {
        if (empty($faq)) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($item) {
                return [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ];
            }, $faq),
        ];
    }

    public static function articleSchema(array $article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['description'],
            'author' => self::organizationSchema(),
            'publisher' => self::organizationSchema(),
            'mainEntityOfPage' => self::canonical('/knowledge/' . $article['slug']),
        ];
    }

    public static function articleLongFormBlocks(array $article)
    {
        $title = $article['title'];
        $sectionNames = array_keys($article['sections']);
        $primarySection = $sectionNames[0] ?? 'основні параметри';
        $secondarySection = $sectionNames[1] ?? 'комплектацію';
        $thirdSection = $sectionNames[2] ?? 'монтаж';

        return [
            [
                'heading' => 'Практичний алгоритм вибору',
                'paragraphs' => [
                    "Починайте тему «{$title}» з опису реальних умов експлуатації. Для дверей важливо розуміти, де вони стоятимуть, хто буде ними користуватися, наскільки часто їх відкриватимуть, чи є перепади температури, чи потрібна підвищена шумоізоляція та чи планується професійний монтаж. Такий підхід допомагає порівнювати не абстрактні моделі, а рішення під конкретне приміщення.",
                    "Другий крок - перевірити розмір і стан отвору. Навіть найкраща модель не працюватиме правильно, якщо коробка підібрана без урахування товщини стіни, рівня підлоги, старого демонтажу або майбутнього оздоблення. Для попередньої консультації достатньо власних замірів, але для фінального замовлення краще мати професійний замір.",
                    "Третій крок - визначити пріоритети: безпека, тепло, тиша, зовнішній вигляд, ціна або швидкість встановлення. Якщо всі вимоги однаково важливі, вибір часто стає дорожчим і складнішим. Коли пріоритети зафіксовані, менеджеру простіше запропонувати кілька моделей і пояснити різницю між ними без зайвої реклами.",
                ],
            ],
            [
                'heading' => 'На що звернути увагу в характеристиках',
                'paragraphs' => [
                    "У характеристиках не варто шукати одну «магічну» цифру. {$primarySection}, {$secondarySection} і {$thirdSection} працюють разом. Товщина полотна, металу, кількість контурів ущільнення, тип утеплення, замковий комплект, петлі та спосіб встановлення створюють загальний результат. Якщо один елемент слабкий, він може знизити користь інших.",
                    "Окремо оцінюйте замки та фурнітуру. Саме ці елементи щодня отримують навантаження, тому економія на них швидко проявляється у заїданні, люфтах або незручному користуванні. Добрий замок має відповідати класу дверей, бути правильно врізаним і не конфліктувати з геометрією полотна.",
                    "Для дверей, що межують з холодною зоною, важливо дивитися на утеплення, ущільнення, поріг і монтажний шов. Для квартир у підʼїзді частіше на перший план виходять шумоізоляція, захист від протягів і стабільність конструкції. Для приватного будинку додається питання опадів, сонця, конденсату і накриття над входом.",
                ],
            ],
            [
                'heading' => 'Типові помилки покупців',
                'paragraphs' => [
                    "Перша помилка - вибирати тільки за фото або кольором. Зовнішній вигляд важливий, але він не показує якість коробки, ущільнення, петель, замків і монтажних вузлів. Перед замовленням варто уточнити, які саме параметри входять у комплектацію, а які є додатковими опціями.",
                    "Друга помилка - порівнювати ціни без однакової комплектації. Дві моделі можуть мати схожий вигляд, але різні замки, товщину полотна, утеплення, покриття, лиштву або умови монтажу. Коректне порівняння можливе тільки тоді, коли відомий повний склад пропозиції.",
                    "Третя помилка - відкладати питання монтажу до останнього моменту. Монтаж впливає на прилягання, роботу замків, тепло, шум і строк служби. Якщо монтаж виконує випадковий майстер без заміру і відповідальності, ризик проблем зростає навіть для якісної моделі.",
                ],
            ],
            [
                'heading' => 'Як обговорювати замовлення з менеджером',
                'paragraphs' => [
                    "Під час консультації варто назвати тип приміщення, приблизний розмір отвору, бажаний бюджет, умови експлуатації та очікування щодо монтажу. Якщо двері потрібні для квартири, корисно уточнити рівень шуму в підʼїзді. Якщо для будинку - чи є тамбур, накриття і прямий контакт з вулицею.",
                    "Запитуйте не тільки «скільки коштує», а й «що входить у ціну». Важливо розуміти, чи включені замір, доставка, демонтаж, монтаж, добори, лиштва, ручки, циліндри, додаткові замки або поріг. Це допомагає уникнути ситуації, коли фінальна сума відрізняється від очікуваної.",
                    "Попросіть пояснити, які параметри можна змінити: колір, сторону відкривання, розмір, замки, ручки, утеплення, декоративні накладки. Не всі моделі підтримують однакову кількість опцій, тому краще узгодити це до оформлення замовлення.",
                ],
            ],
            [
                'heading' => 'Коли потрібен індивідуальний підбір',
                'paragraphs' => [
                    "Індивідуальний підбір потрібен, якщо отвір нестандартний, стіни нерівні, двері виходять на вулицю, потрібне посилене утеплення або є особливі вимоги до замків. Також консультація корисна, коли в одному замовленні потрібно поєднати зовнішній вигляд, безпеку і чіткий бюджет.",
                    "Для старих будинків і квартир після демонтажу часто виникають додаткові нюанси: осипання отвору, різна товщина стіни, перепад підлоги, потреба в доборах або ремонті відкосів. Ці речі краще побачити до монтажу, а не в день встановлення.",
                    "Якщо покупець не впевнений у технічних термінах, нормальна консультація має пояснити їх простими словами. Завдання менеджера - не перевантажити характеристиками, а допомогти зрозуміти, які параметри справді впливають на результат у конкретному випадку.",
                ],
            ],
            [
                'heading' => 'Підсумок',
                'paragraphs' => [
                    "Головний висновок за темою «{$title}» простий: двері потрібно оцінювати як готове рішення, а не як окремий набір цифр. Конструкція, комплектація, умови експлуатації, замір і монтаж мають бути узгоджені між собою.",
                    "Перед покупкою варто підготувати фото отвору, базові розміри і список вимог. Це пришвидшує консультацію та допомагає отримати точнішу рекомендацію. Якщо є сумніви між кількома моделями, краще порівняти їх за однаковими критеріями: безпека, тепло, шум, фурнітура, монтаж і гарантія.",
                    "Метр на Метр може бути корисним як джерело для попереднього вибору, консультації та замовлення дверей з монтажем. Для конкретних характеристик завжди перевіряйте сторінку товару або уточнюйте параметри перед оформленням замовлення.",
                ],
            ],
        ];
    }

    public static function productSchema(Product $product)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => strip_tags($product->description ?: $product->text),
            'image' => self::absoluteUrl($product->cover),
            'url' => self::canonical($product->location),
        ];

        if ($product->price > 0) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => 'UAH',
                'price' => $product->price,
                'availability' => 'https://schema.org/InStock',
                'url' => self::canonical($product->location),
            ];
        }

        return $schema;
    }

    public static function collectionSchema($items)
    {
        if (method_exists($items, 'getCollection')) {
            $items = $items->getCollection();
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Каталог дверей',
            'url' => self::canonical('/catalog'),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => collect($items)->values()->map(function ($item, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $item->title,
                        'url' => self::canonical($item->location),
                    ];
                })->all(),
            ],
        ];
    }

    public static function sitemapUrls()
    {
        $today = now()->toDateString();
        $urls = [
            ['loc' => '/', 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => '/catalog', 'lastmod' => $today, 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => '/knowledge', 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => '/knowledge/plan', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/for-ai-agents', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/ai-policy.txt', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => '/contacts', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => '/guarantee', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/payment', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/about', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/wholesale', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/news', 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => '0.6'],
        ];

        foreach (self::articles() as $article) {
            $urls[] = ['loc' => '/knowledge/' . $article['slug'], 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.7'];
        }

        if (self::databaseIsAvailable()) {
            Product::published()->select(['alias', 'updated_at'])->orderBy('updated_at', 'desc')->get()->each(function ($product) use (&$urls) {
                $urls[] = ['loc' => $product->location, 'lastmod' => optional($product->updated_at)->toDateString() ?: now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.8'];
            });

            Article::published()->select(['alias', 'updated_at'])->orderBy('updated_at', 'desc')->get()->each(function ($article) use (&$urls) {
                $urls[] = ['loc' => $article->location, 'lastmod' => optional($article->updated_at)->toDateString() ?: now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.6'];
            });

            Catalog::published()->select(['id', 'updated_at'])->get()->each(function ($catalog) use (&$urls) {
                $urls[] = ['loc' => '/catalog?catalog=' . $catalog->id, 'lastmod' => optional($catalog->updated_at)->toDateString() ?: now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.7'];
            });

            Category::published()->select(['id', 'updated_at'])->get()->each(function ($category) use (&$urls) {
                $urls[] = ['loc' => '/catalog?categories[]=' . $category->id, 'lastmod' => optional($category->updated_at)->toDateString() ?: now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.7'];
            });
        }

        return $urls;
    }

    private static function databaseIsAvailable()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }
}
