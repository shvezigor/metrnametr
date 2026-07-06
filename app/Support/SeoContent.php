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

    public static function canonical($path = null)
    {
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

    public static function productSchema(Product $product)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => strip_tags($product->description ?: $product->text),
            'image' => $product->cover,
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
            ['loc' => '/for-ai-agents', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/contacts', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.7'],
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
