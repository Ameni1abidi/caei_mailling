<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\EmailHeaderSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailFooterSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    public function test_non_admin_cannot_access_footer_settings(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('settings.email-footer'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_footer_settings(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.email-footer'));
        $response->assertStatus(200);
        $response->assertSee('Pied de page (Footer)');
    }

    public function test_admin_can_update_footer_settings(): void
    {
        $payload = [
            'show_footer' => '1',
            'show_footer_logo' => '1',
            'footer_title' => 'CAEI TEST TITLE',
            'footer_subtitle' => 'Audit & Expertise Subtitle',
            'footer_disclaimer' => 'Test legal disclaimer',
            'footer_unsubscribe_text' => 'Custom Unsubscribe Notice',
            'footer_extra_text' => 'Extra information line',
        ];

        $response = $this->actingAs($this->admin)->post(route('settings.email-footer.update'), $payload);
        $response->assertRedirect(route('settings.email-footer'));
        $response->assertSessionHas('success');

        $settingsService = app(EmailHeaderSettings::class);
        $saved = $settingsService->get();

        $this->assertTrue($saved['show_footer']);
        $this->assertEquals('CAEI TEST TITLE', $saved['footer_title']);
        $this->assertEquals('Audit & Expertise Subtitle', $saved['footer_subtitle']);
        $this->assertEquals('Test legal disclaimer', $saved['footer_disclaimer']);
        $this->assertEquals('Custom Unsubscribe Notice', $saved['footer_unsubscribe_text']);
        $this->assertEquals('Extra information line', $saved['footer_extra_text']);
    }

    public function test_admin_can_reset_footer_settings(): void
    {
        $settingsService = app(EmailHeaderSettings::class);
        $settingsService->save([
            'footer_title' => 'Temporary Modified Title',
        ]);

        $response = $this->actingAs($this->admin)->post(route('settings.email-footer.reset'));
        $response->assertRedirect(route('settings.email-footer'));

        $saved = $settingsService->get();
        $this->assertEquals('CAEI COMPANY GROUP', $saved['footer_title']);
    }
}
