<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_campaigns_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('campaigns.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_campaign(): void
    {
        $category = Category::create(['name' => 'Assurances']);

        $response = $this->actingAs($this->user)
            ->post(route('campaigns.store'), [
                'nom' => 'Séminaire 2026',
                'objet' => 'Invitation',
                'contenu' => 'Bonjour {{Nom}} {{Prenom}}',
                'category_id' => $category->id,
            ]);

        $campaign = Campaign::first();
        $this->assertNotNull($campaign);
        $this->assertEquals('Séminaire 2026', $campaign->nom);
        $this->assertEquals($category->id, $campaign->category_id);

        $response->assertRedirect(route('campaigns.edit', $campaign));
    }

    public function test_can_update_campaign(): void
    {
        $campaign = Campaign::create([
            'nom' => 'Séminaire 2026',
            'objet' => 'Invitation',
            'contenu' => 'Bonjour {{Nom}} {{Prenom}}',
            'created_by' => $this->user->id,
        ]);

        $category = Category::create(['name' => 'Banques']);

        $response = $this->actingAs($this->user)
            ->put(route('campaigns.update', $campaign), [
                'nom' => 'Séminaire 2026 Modifié',
                'objet' => 'Invitation Nouvelle',
                'contenu' => 'Bonjour {{Nom}} {{Prenom}} !!!',
                'category_id' => $category->id,
            ]);

        $campaign->refresh();
        $this->assertEquals('Séminaire 2026 Modifié', $campaign->nom);
        $this->assertEquals('Invitation Nouvelle', $campaign->objet);
        $this->assertEquals($category->id, $campaign->category_id);

        $response->assertRedirect(route('campaigns.edit', $campaign));
    }

    public function test_can_preview_campaign_with_contact(): void
    {
        $category = Category::create(['name' => 'Banques']);
        $contact = Contact::create([
            'nom' => 'Kouassi',
            'prenom' => 'Jean',
            'email' => 'jean.kouassi@example.com',
            'entreprise' => 'Banque Nationale',
            'fonction' => 'Directeur',
        ]);
        $category->contacts()->attach($contact->id);

        $campaign = Campaign::create([
            'nom' => 'Campagne IA',
            'objet' => 'Bonjour {{Nom}}',
            'contenu' => 'Cher {{Prenom}} de {{Entreprise}},',
            'category_id' => $category->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('campaigns.preview', $campaign));

        $response->assertStatus(200);
        $response->assertSee('Bonjour Kouassi');
        $response->assertSee('Cher Jean de Banque Nationale');
    }
}
