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
            ['Двері вхідін квартирні', 'Двері вхідні квартирні'],
            ['Двері вхідіні Імідж', 'Двері вхідні Імідж'],
            ['вуличні (зовнішіні)', 'вуличні (зовнішні)'],
            ['Накладки МДФ с двух сторін 16мм', 'Накладки МДФ з двох сторін 16мм'],
            ['Накладки МДФ с двох сторін 16мм', 'Накладки МДФ з двох сторін 16мм'],
            ['Накладки МДФ С двох сторін 16мм', 'Накладки МДФ З двох сторін 16мм'],
            ['В комплект входить поріг', 'До комплекту входить поріг'],
            ['Двері Підїздні', 'Двері Під’їзні'],
            ['2 контура на створці', '2 контури на створці'],
            ['Патина на 1 сторону', 'Патина з одного боку'],
            ['додатковий завіс', 'додаткова завіса'],
        ];
    }

    public function testItPreservesHtmlBrandsCodesMeasurementsAndCorrectCopy()
    {
        $copy = '<p>Двері вхідні S.A.P., МД 068, 86*203, лист 1.5 мм, МДФ з двох сторін.</p>';

        $this->assertSame($copy, ProductCopy::normalize($copy));
        $this->assertNull(ProductCopy::normalize(null));
    }
}
