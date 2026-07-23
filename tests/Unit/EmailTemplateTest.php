<?php

namespace Tests\Unit;

use App\Models\EmailTemplate;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    public function test_render_blocks_generates_html_for_supported_block_types(): void
    {
        $blocks = [
            [
                'type' => 'text',
                'content' => 'Bonjour {{prenom}}',
                'align' => 'left',
            ],
            [
                'type' => 'button',
                'label' => 'Découvrir',
                'url' => '{{lien}}',
                'color' => '#2563eb',
            ],
            [
                'type' => 'image',
                'src' => '/storage/logo.png',
                'alt' => 'Logo CAEI',
                'width' => 220,
            ],
            [
                'type' => 'signature',
                'content' => 'L’équipe CAEI',
            ],
        ];

        $html = EmailTemplate::renderBlocks($blocks);

        $this->assertStringContainsString('<p', $html);
        $this->assertStringContainsString('Bonjour', $html);
        $this->assertStringContainsString('href=', $html);
        $this->assertStringContainsString('Logo CAEI', $html);
        $this->assertStringContainsString('L’équipe CAEI', $html);
    }
}
