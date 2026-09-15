<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignScopingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $userA;
    protected User $userB;
    protected Campaign $campaignA;
    protected Campaign $campaignB;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->userA = User::factory()->create(['name' => 'User Alpha']);
        $this->userB = User::factory()->create(['name' => 'User Beta']);

        $this->campaignA = Campaign::create([
            'nom'        => 'Campagne de Alpha',
            'objet'      => 'Sujet A',
            'contenu'    => '<p>Contenu Alpha valide</p>',
            'statut'     => 'brouillon',
            'created_by' => $this->userA->id,
        ]);

        $this->campaignB = Campaign::create([
            'nom'        => 'Campagne de Beta',
            'objet'      => 'Sujet B',
            'contenu'    => '<p>Contenu Beta valide</p>',
            'statut'     => 'brouillon',
            'created_by' => $this->userB->id,
        ]);
    }

    /** A standard user only sees their own campaigns in index. */
    public function test_user_only_sees_own_campaigns_in_index(): void
    {
        $response = $this->actingAs($this->userA)->get(route('campaigns.index'));
        $response->assertStatus(200);
        $response->assertSee('Campagne de Alpha');
        $response->assertDontSee('Campagne de Beta');
    }

    /** Admin sees all campaigns in index. */
    public function test_admin_sees_all_campaigns_in_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('campaigns.index'));
        $response->assertStatus(200);
        $response->assertSee('Campagne de Alpha');
        $response->assertSee('Campagne de Beta');
        $response->assertSee('Vue Admin');
    }

    /** User can edit their own campaign. */
    public function test_user_can_edit_own_campaign(): void
    {
        $response = $this->actingAs($this->userA)->get(route('campaigns.edit', $this->campaignA));
        $response->assertStatus(200);
    }

    /** User gets 403 trying to edit another user's campaign. */
    public function test_user_cannot_edit_other_users_campaign(): void
    {
        $response = $this->actingAs($this->userA)->get(route('campaigns.edit', $this->campaignB));
        $response->assertStatus(403);
    }

    /** User gets 403 trying to delete another user's campaign. */
    public function test_user_cannot_delete_other_users_campaign(): void
    {
        $response = $this->actingAs($this->userA)->delete(route('campaigns.destroy', $this->campaignB));
        $response->assertStatus(403);
    }

    /** User gets 403 trying to preview another user's campaign. */
    public function test_user_cannot_preview_other_users_campaign(): void
    {
        $response = $this->actingAs($this->userA)->get(route('campaigns.preview', $this->campaignB));
        $response->assertStatus(403);
    }

    /** User gets 403 trying to send another user's campaign. */
    public function test_user_cannot_send_other_users_campaign(): void
    {
        $response = $this->actingAs($this->userA)->post(route('campaigns.send', $this->campaignB));
        $response->assertStatus(403);
    }

    /** User gets 403 trying to cancel another user's campaign. */
    public function test_user_cannot_cancel_other_users_campaign(): void
    {
        $response = $this->actingAs($this->userA)->post(route('campaigns.cancel', $this->campaignB));
        $response->assertStatus(403);
    }

    /** Admin can edit any user's campaign. */
    public function test_admin_can_edit_any_campaign(): void
    {
        $response = $this->actingAs($this->admin)->get(route('campaigns.edit', $this->campaignA));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('campaigns.edit', $this->campaignB));
        $response->assertStatus(200);
    }

    /** Admin can delete any user's campaign. */
    public function test_admin_can_delete_any_campaign(): void
    {
        $extraCampaign = Campaign::create([
            'nom'        => 'Campagne à supprimer',
            'objet'      => 'Test',
            'contenu'    => '<p>Test</p>',
            'statut'     => 'brouillon',
            'created_by' => $this->userB->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('campaigns.destroy', $extraCampaign));
        $response->assertRedirect(route('campaigns.index'));
        $this->assertDatabaseMissing('campaigns', ['id' => $extraCampaign->id]);
    }
}
