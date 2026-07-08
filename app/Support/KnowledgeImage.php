<?php

namespace App\Support;

class KnowledgeImage
{
    public static function forArticle(array $article)
    {
        $slug = $article['slug'];
        $bucket = self::classify($article);
        $filename = $slug . '.webp';
        $raster = '/images/knowledge/' . $filename;
        $fallback = '/knowledge/' . $slug . '/image.svg';
        $src = self::rasterExists($filename) ? $raster : $fallback;

        return [
            'filename' => $filename,
            'raster' => $raster,
            'fallback' => $fallback,
            'src' => $src,
            'alt' => sprintf('%s для статті %s', $bucket['alt'], $article['title']),
            'title' => $article['title'],
            'caption' => sprintf('%s: %s', $bucket['caption'], $article['title']),
            'prompt' => self::prompt($article, $bucket),
            'topic' => $bucket['topic'],
        ];
    }

    public static function classify(array $article)
    {
        $slug = $article['slug'];

        foreach (self::buckets() as $bucket) {
            foreach ($bucket['keywords'] as $keyword) {
                if (strpos($slug, $keyword) !== false) {
                    return $bucket;
                }
            }
        }

        return self::buckets()['general'];
    }

    public static function rasterExists($filename)
    {
        return file_exists(public_path('images/knowledge/' . $filename));
    }

    private static function prompt(array $article, array $bucket)
    {
        return sprintf(
            'Realistic commercial photo for a Ukrainian door market article about %s. Scene: %s. Use a practical Ukrainian apartment, private house, office, warehouse, workshop, or production environment as appropriate. Clean natural lighting, professional commercial photography, realistic materials and proportions, no luxury foreign showroom look, no abstract 3D background, no text, no logos, no watermarks, horizontal 16:9 composition.',
            $article['title'],
            $bucket['scene']
        );
    }

    private static function buckets()
    {
        return [
            'apartment_entrance' => [
                'topic' => 'вхідні двері для квартири',
                'keywords' => ['kvartyry', 'novobudovy', 'staroyi-kvartyry', 'panelnyi-budynok'],
                'alt' => 'Вхідні двері для квартири у сучасному українському під’їзді',
                'caption' => 'Тематична обкладинка про вибір дверей для квартири',
                'scene' => 'modern metal entrance doors in an apartment corridor or stairwell, reliable lock hardware, clean walls, practical everyday context',
            ],
            'private_house' => [
                'topic' => 'вхідні двері для приватного будинку',
                'keywords' => ['pryvatnoho-budynku', 'budynku', 'kotedzhu', 'fasad', 'klimatom', 'opadiv', 'vulytsi'],
                'alt' => 'Вуличні вхідні двері у приватному будинку',
                'caption' => 'Тематична обкладинка про двері для приватного будинку',
                'scene' => 'street-facing metal entrance doors installed in a modern Ukrainian private house facade, visible threshold and exterior finish',
            ],
            'interior' => [
                'topic' => 'міжкімнатні двері',
                'keywords' => ['mizhkimnatni', 'mizhkimnatnykh', 'interieri', 'minimalistychni', 'klasychni', 'pidlohoiu', 'mebliamy'],
                'alt' => 'Міжкімнатні двері у світлому житловому інтер’єрі',
                'caption' => 'Тематична обкладинка про міжкімнатні двері',
                'scene' => 'interior doors in a bright practical apartment room, clean wall finish, simple hardware, realistic lived-in residential context',
            ],
            'commercial' => [
                'topic' => 'технічні та комерційні двері',
                'keywords' => ['ofisu', 'mahazynu', 'komertsiinoho', 'komertsiinykh', 'obiektiv', 'orendnoho'],
                'alt' => 'Двері для офісного або комерційного приміщення',
                'caption' => 'Тематична обкладинка про двері для комерційних приміщень',
                'scene' => 'technical or commercial doors in an office corridor, store entrance, warehouse, or public building, practical durable finish',
            ],
            'fire_security' => [
                'topic' => 'протипожежні та захисні двері',
                'keywords' => ['protypylezhni', 'protypozhezhni', 'broniovani', 'zlamostiikist', 'antyvandalni', 'bezpeka'],
                'alt' => 'Металеві протипожежні або захисні двері у технічному коридорі',
                'caption' => 'Тематична обкладинка про безпеку та спеціальні двері',
                'scene' => 'strict technical metal doors in an office, warehouse, or public-building corridor, visible hinges and robust handle, no fire or dramatic effects',
            ],
            'locks' => [
                'topic' => 'замки та дверна фурнітура',
                'keywords' => ['zamky', 'zamka', 'tsylindr', 'suvaldnyi', 'furnitury', 'furnituroiu', 'vysverdlennia', 'vybuvannia'],
                'alt' => 'Дверний замок, ручка та фурнітура на металевих дверях',
                'caption' => 'Тематична обкладинка про замки та фурнітуру',
                'scene' => 'close-up of a metal entrance door lock, handle, cylinder, strike plate, and hardware details, clean background',
            ],
            'installation' => [
                'topic' => 'монтаж дверей',
                'keywords' => ['montazh', 'zamiriaty', 'zamir', 'otvir', 'otvoru', 'demontazh', 'zazory', 'korobku', 'ankeruvannia', 'hermetyzatsiia', 'hazobeton', 'tseglu', 'dobory', 'lyshhtva', 'rehuliuvannia', 'zaiidaiut', 'pryiniaty-robotu'],
                'alt' => 'Монтаж дверей майстром у квартирі або будинку',
                'caption' => 'Тематична обкладинка про монтаж дверей',
                'scene' => 'door installation process with a craftsman checking frame alignment, visible tools, wall opening, mounting seam, and new doors',
            ],
            'production' => [
                'topic' => 'виробництво та конструкція дверей',
                'keywords' => ['budova', 'konstruktsiia', 'karkas', 'rebra', 'tovshchyna', 'metalu', 'polotno', 'korobka', 'petli', 'porih', 'sklopakety'],
                'alt' => 'Виробництво або конструкція металевих дверей у цеху',
                'caption' => 'Тематична обкладинка про конструкцію та виробництво дверей',
                'scene' => 'clean door production workshop with metal door leaves, frames, construction details, or preparation stage visible',
            ],
            'insulation' => [
                'topic' => 'утеплення, терморозрив і шумоізоляція',
                'keywords' => ['termo', 'uteplyuvach', 'ushchilnennia', 'shumoizoliatsiia', 'enerhoefektyvnist', 'kondensat', 'tochka-rosy', 'mistok-kholodu'],
                'alt' => 'Утеплені вхідні двері з акцентом на щільне прилягання та ізоляцію',
                'caption' => 'Тематична обкладинка про тепло- та шумоізоляцію дверей',
                'scene' => 'insulated exterior entrance doors in cold weather or a private-house entry area, emphasis on tight seals, threshold, and solid construction',
            ],
            'coating_design_care' => [
                'topic' => 'покриття, дизайн і догляд за дверима',
                'keywords' => ['pokryttia', 'mdf', 'kolir', 'dyzain', 'matovi', 'hliantsevi', 'skliannymy', 'dohliadaty', 'myty', 'skryp', 'rehuliuvaty', 'prosyly', 'remont-pokryttia', 'harantiia', 'servis'],
                'alt' => 'Покриття, колір або догляд за дверима у реальному інтер’єрі',
                'caption' => 'Тематична обкладинка про покриття, дизайн або догляд за дверима',
                'scene' => 'realistic door surface, MDF/PVC/Polymer finish, color and texture details, or simple care/service context in a practical room',
            ],
            'general' => [
                'topic' => 'вибір дверей',
                'keywords' => ['vybraty', 'obraty', 'porivniuvaty', 'kharakterystyky', 'biudzhetom', 'zamovlennia', 'koshtuiut', 'vartist', 'vyhotovlennia', 'nestandartnoho', 'harantiia'],
                'alt' => 'Вибір вхідних та міжкімнатних дверей у практичному українському контексті',
                'caption' => 'Тематична обкладинка про вибір дверей',
                'scene' => 'practical comparison of entrance and interior doors in a showroom-workshop context, realistic models, hardware, and construction details',
            ],
        ];
    }
}
