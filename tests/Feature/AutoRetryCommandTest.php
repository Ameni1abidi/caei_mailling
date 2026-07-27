<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AutoRetryCommandTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_auto_retry_command_picks_up_failed_logs_below_max_retries(): void
    {
        Queue::fake();

        $contact = Contact::create([
            'nom' => 'Kouassi',
            'prenom' => 'Jean',
            'email' => 'jean.kouassi@example.com',
        ]);

        $campaign = Campaign::create([
            'nom' => 'Campagne Auto Retry',
            'objet' => 'Invitation',
            'contenu' => 'Bonjour',
            'statut' => 'envoyee',
            'auto_retry' => true,
            'max_auto_retries' => 3,
            'created_by' => $this->user->id,
        ]);

        $failedLog = EmailLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'status' => EmailLog::STATUS_FAILED,
            'retry_count' => 0,
            'error_message' => 'Timeout',
        ]);

        $this->artisan('campaigns:auto-retry')
            ->assertExitCode(0);

        $this->assertSame(1, $failedLog->fresh()->retry_count);
        $this->assertSame(EmailLog::STATUS_PENDING, $failedLog->fresh()->status);
        $this->assertNull($failedLog->fresh()->error_message);
        $this->assertSame('en_cours', $campaign->fresh()->statut);
        Queue::assertPushed(\App\Jobs\SendCampaignEmailJob::class);
    }

    public function test_auto_retry_command_ignores_logs_reaching_max_retries(): void
    {
        Queue::fake();

        $contact = Contact::create([
            'nom' => 'Kouassi',
            'prenom' => 'Jean',
            'email' => 'jean.kouassi@example.com',
        ]);

        $campaign = Campaign::create([
            'nom' => 'Campagne Max Retries',
            'objet' => 'Invitation',
            'contenu' => 'Bonjour',
            'statut' => 'envoyee',
            'auto_retry' => true,
            'max_auto_retries' => 2,
            'created_by' => $this->user->id,
        ]);

        $failedLog = EmailLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'status' => EmailLog::STATUS_FAILED,
            'retry_count' => 2, // Reached max
            'error_message' => 'Max retries reached',
        ]);

        $this->artisan('campaigns:auto-retry')
            ->assertExitCode(0);

        $this->assertSame(2, $failedLog->fresh()->retry_count);
        $this->assertSame(EmailLog::STATUS_FAILED, $failedLog->fresh()->status);
        Queue::assertNothingPushed();
    }
}
