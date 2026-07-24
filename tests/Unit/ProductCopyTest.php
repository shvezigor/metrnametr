<?php

namespace Tests\Unit;

use App\Support\ProductCopy;
use Tests\TestCase;

class ProductCopyTest extends TestCase
{
    /**
     * @dataProvider incorrectCopyProvider
     */
    public function testItNormalizesReviewedUkrainianMistakes($dirty, $clean)
    {
        $this->assertSame($clean, ProductCopy::normalize($dirty));
    }

    public function incorrectCopyProvider()
    {
        return [
            ['Р”РІРµСЂС– РІС…С–РґС–РЅ РєРІР°СЂС‚РёСЂРЅС–', 'Р”РІРµСЂС– РІС…С–РґРЅС– РєРІР°СЂС‚РёСЂРЅС–'],
            ['Р”РІРµСЂС– РІС…С–РґС–РЅС– Р†РјС–РґР¶', 'Р”РІРµСЂС– РІС…С–РґРЅС– Р†РјС–РґР¶'],
            ['РІСѓР»РёС‡РЅС– (Р·РѕРІРЅС–С€С–РЅС–)', 'РІСѓР»РёС‡РЅС– (Р·РѕРІРЅС–С€РЅС–)'],
            ['РќР°РєР»Р°РґРєРё РњР”Р¤ СЃ РґРІСѓС… СЃС‚РѕСЂС–РЅ 16РјРј', 'РќР°РєР»Р°РґРєРё РњР”Р¤ Р· РґРІРѕС… СЃС‚РѕСЂС–РЅ 16РјРј'],
            ['Р’ РєРѕРјРїР»РµРєС‚ РІС…РѕРґРёС‚СЊ РїРѕСЂС–Рі', 'Р”Рѕ РєРѕРјРїР»РµРєС‚Сѓ РІС…РѕРґРёС‚СЊ РїРѕСЂС–Рі'],
            ['Р”РІРµСЂС– РџС–РґС—Р·РґРЅС–', 'Р”РІРµСЂС– РџС–РґвЂ™С—Р·РЅС–'],
            ['2 РєРѕРЅС‚СѓСЂР° РЅР° СЃС‚РІРѕСЂС†С–', '2 РєРѕРЅС‚СѓСЂРё РЅР° СЃС‚РІРѕСЂС†С–'],
            ['РџР°С‚РёРЅР° РЅР° 1 СЃС‚РѕСЂРѕРЅСѓ', 'РџР°С‚РёРЅР° Р· РѕРґРЅРѕРіРѕ Р±РѕРєСѓ'],
            ['РґРѕРґР°С‚РєРѕРІРёР№ Р·Р°РІС–СЃ', 'РґРѕРґР°С‚РєРѕРІР° Р·Р°РІС–СЃР°'],
        ];
    }

    public function testItPreservesHtmlBrandsCodesMeasurementsAndCorrectCopy()
    {
        $copy = '<p>Р”РІРµСЂС– РІС…С–РґРЅС– S.A.P., РњР” 068, 86*203, Р»РёСЃС‚ 1.5 РјРј, РњР”Р¤ Р· РґРІРѕС… СЃС‚РѕСЂС–РЅ.</p>';

        $this->assertSame($copy, ProductCopy::normalize($copy));
        $this->assertNull(ProductCopy::normalize(null));
    }
}
