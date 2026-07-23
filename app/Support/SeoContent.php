<?php

namespace App\Support;

use App\Models\Article;
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
        $configuredArticles = collect(config('knowledge_articles.articles', []))
            ->map(function ($article) {
                return KnowledgeArticleFactory::make($article);
            });

        $plannedArticles = KnowledgePlan::articles()
            ->map(function ($article) {
                return KnowledgeArticleFactory::make($article);
            });

        return collect(config('seo_content.articles', []))
            ->merge($configuredArticles)
            ->merge($plannedArticles)
            ->unique('slug')
            ->map(function ($article) {
                if (empty($article['image'])) {
                    $article['image'] = KnowledgeImage::forArticle($article);
                }

                return $article;
            })
            ->values();
    }

    public static function article($slug)
    {
        return self::articles()->firstWhere('slug', $slug);
    }

    public static function faq($key = 'door_faq')
    {
        return config("seo_content.{$key}", []);
    }

    public static function landingPages()
    {
        return collect(array_replace(
            config('seo_landings', []),
            config('seo_landings_overrides', []),
            config('seo_commercial_landings', [])
        ));
    }

    public static function landingPage($slug)
    {
        $landing = self::landingPages()->get($slug);

        if (empty($landing)) {
            return null;
        }

        return array_replace_recursive(
            $landing,
            config("seo_commercial_extensions.{$slug}", [])
        );
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

    public static function productSpecifications(Product $product)
    {
        $values = self::decodedList($product->extra_fields);
        $keys = ['door_type', 'installation_place', 'sku', 'mpn', 'brand', 'availability', 'price', 'currency', 'width', 'height', 'leaf_thickness', 'metal_thickness', 'frame', 'insulation', 'thermal_break', 'locks', 'finish', 'color', 'warranty', 'certification', 'installation_available', 'delivery_regions'];

        return collect($keys)->mapWithKeys(function ($key) use ($values) {
            $value = isset($values[$key]) ? trim((string) $values[$key]) : null;

            return [$key => $value ?: null];
        })->all();
    }

    public static function titleFor($model, $fallback)
    {
        return !empty($model->seo_title) ? $model->seo_title : $fallback;
    }

    public static function descriptionFor($model, $fallback = null)
    {
        return !empty($model->seo_description) ? $model->seo_description : ($fallback ?: $model->description);
    }

    public static function productMetaDescription(Product $product)
    {
        if (!empty($product->seo_description)) {
            return $product->seo_description;
        }

        $categories = $product->relationLoaded('categories')
            ? $product->categories
            : $product->categories()->with('type')->get();

        $category = $categories->first();
        $type = $category && $category->relationLoaded('type') ? $category->type : ($category ? $category->type()->first() : null);
        $specifications = self::productSpecifications($product);
        $typeTitle = $specifications['door_type'] ?: ($type ? $type->title : null);
        $categoryTitle = $category ? $category->title : null;
        $purpose = self::productPurpose($specifications, $typeTitle, $categoryTitle);
        $parts = array_filter([$product->title, $typeTitle, $purpose, $specifications['brand']]);

        if ($categoryTitle) {
            $parts[] = 'категорія ' . $categoryTitle;
        }

        return implode(' — ', $parts) . '. Підбір за підтвердженими характеристиками моделі.';
    }

    private static function productPurpose(array $specifications, $typeTitle, $categoryTitle)
    {
        if (!empty($specifications['installation_place'])) {
            return 'для ' . $specifications['installation_place'];
        }

        $text = mb_strtolower($typeTitle . ' ' . $categoryTitle);

        if (strpos($text, 'термо') !== false || strpos($text, 'будин') !== false || strpos($text, 'котедж') !== false) {
            return 'для будинку';
        }

        if (strpos($text, 'міжкім') !== false || strpos($text, 'mizh') !== false) {
            return 'для інтерʼєру';
        }

        if (strpos($text, 'протипож') !== false || strpos($text, 'пожеж') !== false) {
            return 'для технічних і комерційних приміщень';
        }

        return null;
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
            '@id' => self::site('domain') . '/#organization',
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
            '@id' => self::site('domain') . '/#website',
            'name' => self::site('name'),
            'url' => self::site('domain'),
            'publisher' => ['@id' => self::site('domain') . '/#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => self::canonical('/catalog') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function localBusinessSchema()
    {
        $googleMapsUrl = 'https://www.google.com/maps?cid=15751063054979951698';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => self::site('domain') . '/#localbusiness',
            'name' => self::site('name'),
            'parentOrganization' => ['@id' => self::site('domain') . '/#organization'],
            'url' => self::site('domain') . '/',
            'image' => self::canonical(self::site('default_image')),
            'hasMap' => $googleMapsUrl,
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 50.754183,
                'longitude' => 25.3356367,
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Луцьк',
                'addressRegion' => 'Волинська область',
                'postalCode' => '43000',
                'addressCountry' => 'UA',
                'streetAddress' => 'проспект Перемоги, 24',
            ],
            'telephone' => self::site('phone'),
            'areaServed' => [
                'Луцьк',
                'Волинська область',
                'Ковель',
                'Володимир',
                'Нововолинськ',
                'Рівне',
            ],
            'sameAs' => [
                $googleMapsUrl,
                'https://www.facebook.com/Metr-Door-%D0%A2%D0%9C-%D0%9C%D0%B5%D1%82%D1%80-%D0%BD%D0%B0-%D0%9C%D0%B5%D1%82%D1%80-543983232393683/',
                'https://www.instagram.com/metrdoor/',
                'https://www.youtube.com/channel/UCq6UHUwphtD8tCnsCTe2rFw/featured',
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

    public static function landingPageSchema(array $landing)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $landing['h1'],
            'description' => $landing['description'],
            'url' => self::canonical($landing['path']),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => self::site('name'),
                'url' => self::site('domain'),
            ],
        ];
    }

    public static function serviceSchema(array $landing)
    {
        if (empty($landing['service'])) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $landing['service']['name'] ?? $landing['h1'],
            'serviceType' => $landing['service']['serviceType'] ?? $landing['h1'],
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => self::site('name'),
                'telephone' => self::site('phone'),
                'url' => self::site('domain'),
            ],
            'areaServed' => array_map(function ($area) {
                return [
                    '@type' => 'Place',
                    'name' => $area,
                ];
            }, $landing['service']['areaServed'] ?? ['Луцьк']),
            'url' => self::canonical($landing['path']),
        ];
    }

    public static function realWorksGallerySchema($cases)
    {
        $images = collect($cases)->flatMap(function ($case) {
            return collect($case['images'])->map(function ($image) use ($case) {
                return [
                    '@type' => 'ImageObject',
                    'name' => $case['title'],
                    'caption' => $image['alt'],
                    'contentUrl' => self::canonical($image['jpg']),
                    'thumbnailUrl' => self::canonical($image['webp']),
                    'width' => $image['width'],
                    'height' => $image['height'],
                ];
            });
        })->values()->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ImageGallery',
            'name' => RealWorks::page()['h1'],
            'description' => RealWorks::page()['description'],
            'url' => self::canonical(RealWorks::page()['path']),
            'associatedMedia' => $images,
        ];
    }

    public static function imageObjectSchema($image, $name, $caption = null)
    {
        if (empty($image)) {
            return null;
        }

        $path = parse_url($image, PHP_URL_PATH);

        if ($path && strpos($path, '/') === 0 && !is_file(public_path(ltrim($path, '/')))) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ImageObject',
            'name' => $name,
            'caption' => $caption ?: $name,
            'contentUrl' => self::absoluteUrl($image),
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
            'image' => self::articleImageUrl($article),
            'author' => self::organizationSchema(),
            'publisher' => self::organizationSchema(),
            'mainEntityOfPage' => self::canonical('/knowledge/' . $article['slug']),
        ];
    }

    public static function articleImageUrl(array $article)
    {
        $image = $article['image'] ?? KnowledgeImage::forArticle($article);

        return self::canonical($image['src']);
    }

    public static function articleImageSvg(array $article)
    {
        $title = htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');
        $slug = $article['slug'];
        $hash = substr(md5($slug), 0, 6);
        $accent = '#' . $hash;
        $dark = '#1f2933';
        $muted = '#64748b';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675" viewBox="0 0 1200 675" role="img" aria-labelledby="title desc">
  <title id="title">{$title}</title>
  <desc id="desc">Технічна ілюстрація Метр на Метр для статті про двері</desc>
  <rect width="1200" height="675" fill="#f5f7fa"/>
  <rect x="70" y="70" width="1060" height="535" rx="28" fill="#ffffff" stroke="#d9e2ec" stroke-width="2"/>
  <rect x="118" y="118" width="310" height="440" rx="10" fill="#eef2f6" stroke="{$dark}" stroke-width="10"/>
  <rect x="155" y="155" width="236" height="366" rx="4" fill="#ffffff" stroke="#94a3b8" stroke-width="4"/>
  <rect x="181" y="185" width="184" height="128" rx="4" fill="#e2e8f0"/>
  <rect x="181" y="336" width="184" height="154" rx="4" fill="#e2e8f0"/>
  <circle cx="353" cy="345" r="13" fill="{$accent}"/>
  <rect x="448" y="128" width="44" height="422" rx="6" fill="{$accent}" opacity="0.2"/>
  <path d="M492 160h195M492 255h145M492 350h225M492 445h170" stroke="{$accent}" stroke-width="8" stroke-linecap="round"/>
  <g transform="translate(730 140)">
    <rect x="0" y="0" width="285" height="170" rx="16" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
    <path d="M42 118V58h52l22 60h36l24-60h64v60" fill="none" stroke="{$dark}" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="94" cy="58" r="13" fill="{$accent}"/>
    <circle cx="176" cy="58" r="13" fill="{$accent}"/>
  </g>
  <g transform="translate(730 345)">
    <rect x="0" y="0" width="285" height="155" rx="16" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
    <rect x="44" y="54" width="190" height="58" rx="10" fill="#e2e8f0" stroke="{$dark}" stroke-width="5"/>
    <circle cx="84" cy="83" r="12" fill="{$accent}"/>
    <path d="M105 83h92" stroke="{$dark}" stroke-width="7" stroke-linecap="round"/>
  </g>
  <text x="118" y="614" fill="{$muted}" font-family="Arial, sans-serif" font-size="28">Метр на Метр</text>
</svg>
SVG;
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
        $specifications = self::productSpecifications($product);
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => strip_tags($product->description ?: $product->text),
            'image' => self::absoluteUrl($product->cover),
            'url' => self::canonical($product->location),
        ];

        foreach (['sku', 'mpn', 'color'] as $key) {
            if (!empty($specifications[$key])) {
                $schema[$key] = $specifications[$key];
            }
        }

        if (!empty($specifications['brand'])) {
            $schema['brand'] = ['@type' => 'Brand', 'name' => $specifications['brand']];
        }

        if (!empty($specifications['door_type'])) {
            $schema['category'] = $specifications['door_type'];
        }

        if (!empty($specifications['finish'])) {
            $schema['material'] = $specifications['finish'];
        }

        $size = array_filter([$specifications['width'], $specifications['height']]);
        if (!empty($size)) {
            $schema['size'] = implode(' × ', $size);
        }

        if ($product->price > 0) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => 'UAH',
                'price' => $product->price,
                'url' => self::canonical($product->location),
            ];

            $availability = self::availabilitySchemaUrl($specifications['availability']);
            if ($availability) {
                $schema['offers']['availability'] = $availability;
            }
        }

        return $schema;
    }

    private static function availabilitySchemaUrl($availability)
    {
        $values = [
            'instock' => 'https://schema.org/InStock',
            'outofstock' => 'https://schema.org/OutOfStock',
            'preorder' => 'https://schema.org/PreOrder',
            'backorder' => 'https://schema.org/BackOrder',
        ];
        $key = strtolower(str_replace([' ', '-', '_'], '', (string) $availability));

        return $values[$key] ?? null;
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
        $urls = [
            self::staticSitemapUrl('/', 'resources/views/client/main/index.blade.php', 'weekly', '1.0'),
            self::staticSitemapUrl(RealWorks::page()['path'], 'config/real_works.php', 'monthly', '0.8'),
            self::staticSitemapUrl('/catalog', 'resources/views/client/products/index.blade.php', 'daily', '0.9'),
            self::staticSitemapUrl('/knowledge', 'app/Support/KnowledgePlan.php', 'weekly', '0.8'),
            self::staticSitemapUrl('/for-ai-agents', 'resources/views/client/knowledge/for-ai-agents.blade.php', 'monthly', '0.5'),
            self::staticSitemapUrl('/ai-policy.txt', 'app/Http/Controllers/SeoController.php', 'monthly', '0.4'),
            self::staticSitemapUrl('/contacts', 'resources/views/client/main/contacts.blade.php', 'monthly', '0.7'),
            self::staticSitemapUrl('/guarantee', 'resources/views/client/main/guarantee.blade.php', 'monthly', '0.5'),
            self::staticSitemapUrl('/payment', 'resources/views/client/main/payment.blade.php', 'monthly', '0.5'),
            self::staticSitemapUrl('/about', 'resources/views/client/main/about.blade.php', 'monthly', '0.5'),
            self::staticSitemapUrl('/wholesale', 'resources/views/client/main/wholesale.blade.php', 'monthly', '0.5'),
            self::staticSitemapUrl('/news', 'app/Http/Controllers/ArticlesController.php', 'weekly', '0.6'),
        ];

        foreach (self::landingPages() as $landing) {
            $urls[] = self::staticSitemapUrl($landing['path'], 'config/seo_landings.php', 'monthly', '0.8');
        }

        foreach (self::articles() as $article) {
            $urls[] = self::staticSitemapUrl('/knowledge/' . $article['slug'], 'config/seo_content.php', 'monthly', '0.7');
        }

        if (self::databaseIsAvailable()) {
            Product::published()->select(['alias', 'updated_at'])->orderBy('updated_at', 'desc')->get()->each(function ($product) use (&$urls) {
                if ($product->updated_at) {
                    $urls[] = ['loc' => $product->location, 'lastmod' => $product->updated_at->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.8'];
                }
            });

            Article::published()->select(['alias', 'updated_at'])->orderBy('updated_at', 'desc')->get()->each(function ($article) use (&$urls) {
                if ($article->updated_at) {
                    $urls[] = ['loc' => $article->location, 'lastmod' => $article->updated_at->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.6'];
                }
            });

        }

        return collect($urls)
            ->map(function ($url) {
                $url['loc'] = self::sitemapCanonical($url['loc']);

                return $url;
            })
            ->filter(function ($url) {
                $loc = $url['loc'] ?? '';

                return $loc
                    && strpos($loc, self::site('domain')) === 0
                    && strpos($loc, '?') === false
                    && strpos($loc, '#') === false
                    && strpos($loc, '%20') === false
                    && strpos($loc, '...') === false
                    && ($url['lastmod'] ?? null)
                    && $url['lastmod'] <= now()->toDateString();
            })
            ->unique('loc')
            ->values()
            ->all();
    }

    private static function staticSitemapUrl($loc, $source, $changefreq, $priority)
    {
        return [
            'loc' => $loc,
            'lastmod' => self::sourceLastModified($source),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private static function sourceLastModified($source)
    {
        $path = base_path($source);

        if (!is_file($path)) {
            $path = config_path('seo_content.php');
        }

        return now()->setTimestamp(filemtime($path))->toDateString();
    }

    private static function sitemapCanonical($loc)
    {
        $path = parse_url($loc, PHP_URL_PATH) ?: '/';

        return self::canonical($path);
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
