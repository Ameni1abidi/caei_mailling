<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Contact;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSmtpTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_save_user_with_dedicated_ovh_mailbox(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Commercial Jean',
            'email' => 'jean@caei-afri.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'smtp_email' => 'commercial@caei-afri.com',
            'smtp_password' => 'ovhPass456',
            'smtp_sender_name' => 'Jean - Commercial CAEI',
            'smtp_rate_limit' => 3,
        ]);

        $response->assertRedirect(route('users.index'));

        $user = User::where('email', 'jean@caei-afri.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->smtpSetting);
        $this->assertEquals('commercial@caei-afri.com', $user->smtpSetting->sender_email);
        $this->assertEquals('commercial@caei-afri.com', $user->smtpSetting->username);
        $this->assertEquals('Jean - Commercial CAEI', $user->smtpSetting->sender_name);
        $this->assertEquals(3, $user->smtpSetting->rate_limit);
        $this->assertEquals('ssl0.ovh.net', $user->smtpSetting->host);
        $this->assertEquals(587, $user->smtpSetting->port);
        $this->assertEquals('tls', $user->smtpSetting->encryption);
    }

    public function test_can_update_user_mailbox(): void
    {
        $user = User::factory()->create(['name' => 'Sarah']);
        $user->smtpSetting()->create([
            'provider' => 'OVHcloud SMTP',
            'driver' => 'smtp',
            'host' => 'ssl0.ovh.net',
            'port' => 587,
            'username' => 'sarah@caei-afri.com',
            'password' => 'oldPass',
            'encryption' => 'tls',
            'sender_email' => 'sarah@caei-afri.com',
            'sender_name' => 'Sarah',
            'rate_limit' => 3,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => 'Sarah Vente',
            'email' => $user->email,
            'smtp_email' => 'ventes@caei-afri.com',
            'smtp_password' => 'newPass999',
            'smtp_sender_name' => 'Sarah - Ventes',
            'smtp_rate_limit' => 3,
        ]);

        $response->assertRedirect(route('users.index'));
        $user->refresh();
        $this->assertEquals('ventes@caei-afri.com', $user->smtpSetting->sender_email);
        $this->assertEquals('Sarah - Ventes', $user->smtpSetting->sender_name);
    }

    public function test_campaign_resolves_creator_smtp_setting(): void
    {
        $creator = User::factory()->create(['name' => 'Marc']);
        $creatorSmtp = SmtpSetting::create([
            'user_id' => $creator->id,
            'provider' => 'OVHcloud SMTP',
            'driver' => 'smtp',
            'host' => 'ssl0.ovh.net',
            'port' => 587,
            'username' => 'marc@caei-afri.com',
            'password' => 'secret',
            'encryption' => 'tls',
            'sender_email' => 'marc@caei-afri.com',
            'sender_name' => 'Marc Dupont',
            'rate_limit' => 3,
            'is_active' => true,
        ]);

        $campaign = Campaign::create([
            'nom' => 'Campagne Marc',
            'objet' => 'Test',
            'contenu' => 'Bonjour',
            'created_by' => $creator->id,
            'smtp_setting_id' => $creatorSmtp->id,
        ]);

        $resolvedSmtp = $campaign->resolveSmtpSetting();
        $this->assertNotNull($resolvedSmtp);
        $this->assertEquals('marc@caei-afri.com', $resolvedSmtp->sender_email);
    }

    public function test_campaign_falls_back_to_default_smtp_when_no_dedicated_box(): void
    {
        $defaultSmtp = SmtpSetting::create([
            'provider' => 'OVHcloud SMTP',
            'driver' => 'smtp',
            'host' => 'ssl0.ovh.net',
            'port' => 587,
            'username' => 'contact@caei-afri.com',
            'password' => 'mainSecret',
            'encryption' => 'tls',
            'sender_email' => 'contact@caei-afri.com',
            'sender_name' => 'CAEI Officiel',
            'rate_limit' => 3,
            'is_active' => true,
        ]);

        $creatorWithoutSmtp = User::factory()->create();

        $campaign = Campaign::create([
            'nom' => 'Campagne Générale',
            'objet' => 'Test',
            'contenu' => 'Bonjour',
            'created_by' => $creatorWithoutSmtp->id,
        ]);

        $resolvedSmtp = $campaign->resolveSmtpSetting();
        $this->assertNotNull($resolvedSmtp);
        $this->assertEquals('contact@caei-afri.com', $resolvedSmtp->sender_email);
    }
}
