<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_categories_index(): void
    {
        $category = Category::create(['name' => 'Partenaires']);

        $response = $this->actingAs($this->user)
            ->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Partenaires');
    }

    public function test_can_create_category_with_validation(): void
    {
        // Validation failure (missing name)
        $response = $this->actingAs($this->user)
            ->post(route('categories.store'), [
                'name' => '',
            ]);
        $response->assertSessionHasErrors('name');

        // Successful creation
        $response = $this->actingAs($this->user)
            ->post(route('categories.store'), [
                'name' => 'Secteur Public',
                'description' => 'Institutions et ministères',
                'couleur' => '#10b981',
                'icone' => 'building',
            ]);

        $category = Category::where('name', 'Secteur Public')->first();
        $this->assertNotNull($category);
        $this->assertEquals('#10b981', $category->couleur);
        $response->assertRedirect(route('categories.index'));

        // Validation failure (duplicate name)
        $responseDuplicate = $this->actingAs($this->user)
            ->post(route('categories.store'), [
                'name' => 'Secteur Public',
            ]);
        $responseDuplicate->assertSessionHasErrors('name');
    }

    public function test_can_show_category_and_manage_contacts(): void
    {
        $category = Category::create(['name' => 'Finance']);
        $contact1 = Contact::create([
            'nom' => 'Diop',
            'prenom' => 'Amadou',
            'email' => 'amadou.diop@example.com',
        ]);
        $contact2 = Contact::create([
            'nom' => 'Sow',
            'prenom' => 'Fatou',
            'email' => 'fatou.sow@example.com',
        ]);

        // Attach contact1
        $category->contacts()->attach($contact1->id);

        // View show page
        $response = $this->actingAs($this->user)
            ->get(route('categories.show', $category));

        $response->assertStatus(200);
        $response->assertSee('Diop');
        $response->assertSee('Finance');

        // Add contact2 via controller action
        $addResponse = $this->actingAs($this->user)
            ->post(route('categories.addContacts', $category), [
                'contacts' => [$contact2->id],
            ]);

        $addResponse->assertRedirect(route('categories.show', $category));
        $this->assertTrue($category->contacts->contains($contact2->id));

        // Remove contact1 via controller action
        $removeResponse = $this->actingAs($this->user)
            ->delete(route('categories.removeContact', [$category, $contact1]));

        $removeResponse->assertRedirect(route('categories.show', $category));
        $this->assertFalse($category->fresh()->contacts->contains($contact1->id));
    }

    public function test_can_update_category(): void
    {
        $category = Category::create(['name' => 'Ancien Nom']);

        $response = $this->actingAs($this->user)
            ->put(route('categories.update', $category), [
                'name' => 'Nouveau Nom',
                'description' => 'Mise à jour',
                'couleur' => '#ef4444',
            ]);

        $response->assertRedirect(route('categories.index'));
        $category->refresh();
        $this->assertEquals('Nouveau Nom', $category->name);
        $this->assertEquals('#ef4444', $category->couleur);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::create(['name' => 'À supprimer']);
        $contact = Contact::create([
            'nom' => 'Koné',
            'prenom' => 'Moussa',
            'email' => 'moussa.kone@example.com',
        ]);
        $category->contacts()->attach($contact->id);

        $response = $this->actingAs($this->user)
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('category_contact', ['category_id' => $category->id]);
        // Contact itself should still exist
        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);
    }

    public function test_category_can_be_selected_for_campaign(): void
    {
        $category = Category::create(['name' => 'Banques UEMOA']);
        $contact = Contact::create([
            'nom' => 'Diallo',
            'prenom' => 'Ibrahima',
            'email' => 'ibrahima.diallo@example.com',
        ]);
        $category->contacts()->attach($contact->id);

        // Create campaign targeting this category
        $response = $this->actingAs($this->user)
            ->post(route('campaigns.store'), [
                'nom' => 'Offre Bancaire',
                'objet' => 'Nouveau service',
                'contenu' => 'Bonjour {{Nom}}',
                'category_id' => $category->id,
            ]);

        $campaign = Campaign::where('nom', 'Offre Bancaire')->first();
        $this->assertNotNull($campaign);
        $this->assertEquals($category->id, $campaign->category_id);

        // Verify targeted contacts in campaign edit
        $editResponse = $this->actingAs($this->user)
            ->get(route('campaigns.edit', $campaign));

        $editResponse->assertStatus(200);
        $editResponse->assertSee('Banques UEMOA');
    }
}
