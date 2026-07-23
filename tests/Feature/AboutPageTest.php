<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function testAdvantageIconsKeepTheirAspectRatioInCompactContainers()
    {
        $response = $this->get('/about')->assertStatus(200);
        $scss = file_get_contents(resource_path('client/scss/_common.scss'));

        foreach (['wallet', 'catalog', 'door', 'delivery-truck', 'money-bag', 'lock', 'door-knob', 'handshake'] as $icon) {
            $response->assertSee('/images/icons/' . $icon . '.svg', false);
        }
        $this->assertStringContainsString('min-height: 50px;', $scss);
        $this->assertStringContainsString('width: auto;', $scss);
        $this->assertStringContainsString('height: 50px;', $scss);
        $this->assertStringContainsString('max-width: 68px;', $scss);
    }
}
