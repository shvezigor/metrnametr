<?php

namespace App\Support;

class KnowledgePlan
{
    public static function clusters()
    {
        return collect(config('knowledge_plan.clusters', []));
    }

    public static function articles()
    {
        return self::clusters()->flatMap(function ($cluster, $clusterKey) {
            return collect($cluster['titles'])->map(function ($title, $slug) use ($cluster, $clusterKey) {
                return self::articleBrief($clusterKey, $cluster, $slug, $title);
            });
        })->values();
    }

    public static function groupedArticles()
    {
        return self::articles()->groupBy('cluster');
    }

    public static function distribution()
    {
        return self::articles()->countBy('cluster');
    }

    public static function articleBrief($clusterKey, array $cluster, $slug, $title)
    {
        return [
            'cluster' => $clusterKey,
            'cluster_label' => $cluster['label'],
            'title' => $title,
            'slug' => $slug,
            'meta_title' => $title . ' | База знань Метр на Метр',
            'meta_description' => self::description($title, $cluster['label']),
            'h1' => $title,
            'canonical' => SeoContent::canonical('/knowledge/' . $slug),
            'keywords' => self::keywords($title, $cluster['label']),
            'intro_goal' => 'Дати практичну відповідь без SEO-води: коли це рішення підходить, які параметри перевірити, які помилки уникнути і що уточнити перед замовленням.',
            'sections' => self::sections($title, $cluster['label']),
            'table_plan' => 'Порівняти базовий, середній і рекомендований підхід за практичними критеріями теми.',
            'product_links' => [
                '/catalog',
                '/catalog?catalog=1',
                '/catalog?catalog=2',
                '/catalog?categories%5B%5D=1',
                '/catalog?categories%5B%5D=2',
            ],
            'category_links' => [
                '/catalog',
                '/knowledge',
                '/contacts',
                '/guarantee',
                '/payment',
            ],
            'article_links' => [
                '/knowledge/yak-vybraty-vkhidni-dveri-dlia-kvartyry',
                '/knowledge/termo-rozryv-shcho-tse',
                '/knowledge/yaka-tovshchyna-metalu-u-dveriakh',
                '/knowledge/yaki-zamky-krashchi',
                '/knowledge/montazh-vkhidnykh-dverei',
            ],
            'image_prompts' => self::imagePrompts($title, $slug),
            'faq_questions' => self::faqQuestions($title),
            'schema_types' => ['Article', 'FAQPage', 'BreadcrumbList'],
            'status' => 'planned',
        ];
    }

    private static function description($title, $clusterLabel)
    {
        return "{$title}: експертний матеріал Метр на Метр у розділі {$clusterLabel} з практичними критеріями, таблицею, FAQ та порадами перед замовленням дверей.";
    }

    private static function keywords($title, $clusterLabel)
    {
        return collect([$title, $clusterLabel, 'двері', 'Метр на Метр', 'Луцьк'])->implode(', ');
    }

    private static function sections($title, $clusterLabel)
    {
        return [
            'Коротке визначення теми',
            'Коли це питання виникає у покупця',
            'Основні критерії оцінки',
            'Конструктивні параметри дверей',
            'Матеріали, фурнітура і комплектація',
            'Монтажні умови і обмеження',
            'Порівняльна таблиця варіантів',
            'Типові помилки під час вибору або робіт',
            'Практичні поради майстра',
            'Що уточнити перед замовленням',
        ];
    }

    private static function imagePrompts($title, $slug)
    {
        $base = "Professional technical illustration for a Ukrainian door manufacturer knowledge base, topic: {$title}. Clean realistic workshop style, no stock photo look, accurate door construction details, neutral light background, high resolution, no people unless needed, no text overlays.";

        return [
            [
                'type' => 'hero',
                'filename' => $slug . '-hero.webp',
                'alt' => $title,
                'title' => $title,
                'caption' => 'Головна ілюстрація до матеріалу: ' . $title,
                'prompt' => $base . ' Hero image, premium entrance and interior doors arranged in a showroom and workshop context, practical expert mood.',
            ],
            [
                'type' => 'cross-section',
                'filename' => $slug . '-cross-section.webp',
                'alt' => 'Розріз дверей для теми ' . $title,
                'title' => 'Розріз дверей',
                'caption' => 'Схематичний розріз дверного полотна.',
                'prompt' => $base . ' Detailed cutaway cross-section of a door leaf with steel sheet, insulation, ribs, seals, frame and threshold visible.',
            ],
            [
                'type' => 'diagram',
                'filename' => $slug . '-diagram.webp',
                'alt' => 'Схема вузлів дверей: ' . $title,
                'title' => 'Схема вузлів дверей',
                'caption' => 'Технічна схема ключових вузлів.',
                'prompt' => $base . ' Isometric exploded technical diagram of door frame, hinges, lock area, seals, threshold and mounting seam.',
            ],
            [
                'type' => 'lock',
                'filename' => $slug . '-lock-system.webp',
                'alt' => 'Будова замка для дверей',
                'title' => 'Будова замка',
                'caption' => 'Ілюстрація замкового вузла.',
                'prompt' => $base . ' Close-up technical cutaway of a mortise door lock, cylinder, bolts, strike plate and reinforced lock zone.',
            ],
            [
                'type' => 'installation',
                'filename' => $slug . '-installation.webp',
                'alt' => 'Монтажний вузол дверей',
                'title' => 'Монтажний вузол',
                'caption' => 'Приклад монтажного вузла без рекламного стилю.',
                'prompt' => $base . ' Accurate installation detail showing wall opening, door frame alignment, anchors, foam seam, sealant and trims.',
            ],
            [
                'type' => 'infographic',
                'filename' => $slug . '-comparison-infographic.webp',
                'alt' => 'Інфографіка для теми ' . $title,
                'title' => 'Порівняльна інфографіка',
                'caption' => 'Візуальне порівняння варіантів.',
                'prompt' => $base . ' Clean infographic-style comparison of basic, standard and recommended door solutions using visual blocks only, leave space for native HTML labels.',
            ],
        ];
    }

    private static function faqQuestions($title)
    {
        return [
            "Що найважливіше знати про {$title}?",
            "Коли варто обирати це рішення?",
            "Які характеристики потрібно перевірити першими?",
            "Які помилки найчастіше роблять покупці?",
            "Як ця тема впливає на монтаж дверей?",
            "Що залежить від умов квартири або будинку?",
            "Які параметри потрібно уточнити у менеджера?",
            "Чи можна змінити комплектацію під замовлення?",
            "Як порівняти кілька моделей між собою?",
            "Коли потрібна консультація або професійний замір?",
        ];
    }
}
