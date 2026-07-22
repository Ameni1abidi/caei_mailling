<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->user = User::factory()->create();
    }

    public function test_only_admin_can_manage_templates(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('email-templates.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_templates_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('email-templates.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_install_default_templates(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('email-templates.install-defaults'));

        $this->assertDatabaseHas('email_templates', [
            'nom' => 'Invitation seminaire',
            'type' => 'invitation',
            'is_active' => true,
        ]);
        $this->assertDatabaseCount('email_templates', 5);

        $response->assertRedirect(route('email-templates.index'));
    }

    public function test_admin_can_create_template_with_type_and_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('email-templates.store'), [
                'nom' => 'Relance personnalisable',
                'sujet' => 'Suite a notre echange',
                'type' => 'relance',
                'contenu' => 'Bonjour {{prenom}} {{nom}}',
                'is_active' => '1',
            ]);

        $this->assertDatabaseHas('email_templates', [
            'nom' => 'Relance personnalisable',
            'sujet' => 'Suite a notre echange',
            'type' => 'relance',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('email-templates.index'));
    }

    public function test_template_preview_replaces_example_variables(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'Invitation',
            'sujet' => 'Invitation {{nom_seminaire}}',
            'type' => 'invitation',
            'contenu' => 'Bonjour {{prenom}} {{nom}}, lien: {{lien}}',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('email-templates.preview', $template));

        $response->assertStatus(200);
        $response->assertSee('Seminaire Audit & Gouvernance');
        $response->assertSee('Amel');
        $response->assertSee('Ben Ali');
    }

    public function test_admin_can_toggle_template_status(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'Newsletter',
            'sujet' => 'Actualites CAEI',
            'type' => 'newsletter',
            'contenu' => 'Bonjour {{prenom}}, voici nos actualites.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('email-templates.toggle', $template));

        $this->assertFalse($template->fresh()->is_active);
        $response->assertRedirect(route('email-templates.index'));
    }

    public function test_can_use_active_template_to_prefill_campaign_form(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'Newsletter',
            'sujet' => 'Actualites CAEI',
            'type' => 'newsletter',
            'contenu' => 'Bonjour {{prenom}}, voici nos actualites.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('campaigns.create', ['template_id' => $template->id]));

        $response->assertStatus(200);
        $response->assertSee('Newsletter');
        $response->assertSee('Actualites CAEI');
        $response->assertSee('Bonjour {{prenom}}, voici nos actualites.');
    }

    public function test_inactive_template_cannot_prefill_campaign_form(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'Archive',
            'sujet' => 'Ancien sujet',
            'type' => 'newsletter',
            'contenu' => 'Ancien contenu',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('campaigns.create', ['template_id' => $template->id]));

        $response->assertNotFound();
    }

    public function test_admin_can_access_builder_page(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'Builder Test',
            'sujet' => 'Test',
            'type' => 'newsletter',
            'contenu' => 'Bonjour {{prenom}}',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('email-templates.builder', $template));

        $response->assertStatus(200);
        $response->assertSee('Email Builder');
    }

    public function test_admin_can_save_builder_data(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'Builder Test',
            'sujet' => 'Test',
            'type' => 'newsletter',
            'contenu' => 'Bonjour {{prenom}}',
            'is_active' => true,
        ]);

        $gjsData = ['components' => [['type' => 'text', 'content' => 'Hello Visual']]];

        $response = $this->actingAs($this->admin)
            ->postJson(route('email-templates.builder.save', $template), [
                'gjs_data' => $gjsData,
                'html' => '<div>Hello Visual</div>',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $template->refresh();
        $this->assertEquals('<div>Hello Visual</div>', $template->contenu);
        $this->assertEquals($gjsData, $template->gjs_data);
    }

    public function test_admin_can_upload_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test_logo.png', 100, 100);

        $response = $this->actingAs($this->admin)
            ->post(route('email-templates.upload-image'), [
                'files' => [$file],
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $uploadedUrl = $response->json('data.0');
        $this->assertNotNull($uploadedUrl);

        $relativePath = str_replace(asset('storage/'), '', $uploadedUrl);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_image_upload_fails_for_invalid_mime(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.exe', 100, 'application/octet-stream');

        $response = $this->actingAs($this->admin)
            ->post(route('email-templates.upload-image'), [
                'files' => [$file],
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }

    public function test_xss_protection_removes_malicious_scripts_from_builder_html(): void
    {
        $template = EmailTemplate::create([
            'nom' => 'XSS Test',
            'sujet' => 'Test',
            'type' => 'newsletter',
            'contenu' => 'Bonjour',
            'is_active' => true,
        ]);

        $maliciousHtml = '<div>Bonjour <script>alert("XSS")</script></div>';
        $gjsData = ['components' => []];

        $response = $this->actingAs($this->admin)
            ->postJson(route('email-templates.builder.save', $template), [
                'gjs_data' => $gjsData,
                'html' => $maliciousHtml,
            ]);

        $response->assertStatus(200);
        $template->refresh();
        $this->assertStringNotContainsString('<script>', $template->contenu);
        $this->assertStringContainsString('<div>Bonjour </div>', $template->contenu);
    }
}
