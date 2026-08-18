<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the campaigns created by this user.
     */
    public function campaigns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    /**
     * Get the contact import logs by this user.
     */
    public function importLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ImportLog::class, 'user_id');
    }

    /**
     * Calculate monitoring statistics for this user.
     */
    public function getStatsAttribute(): array
    {
        $campaignIds = $this->campaigns()->pluck('id');

        $totalCampaigns = $this->campaigns()->count();
        $sentCampaigns = $this->campaigns()->where('statut', 'envoyee')->count();
        $draftCampaigns = $this->campaigns()->where('statut', 'brouillon')->count();
        $inProgressCampaigns = $this->campaigns()->where('statut', 'en_cours')->count();

        $emailsSent = EmailLog::whereIn('campaign_id', $campaignIds)
            ->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->count();

        $emailsDelivered = EmailLog::whereIn('campaign_id', $campaignIds)
            ->where('status', EmailLog::STATUS_DELIVERED)
            ->count();

        $emailsOpened = EmailLog::whereIn('campaign_id', $campaignIds)
            ->where('opened', true)
            ->count();

        $emailsClicked = EmailLog::whereIn('campaign_id', $campaignIds)
            ->where('clicked', true)
            ->count();

        $emailsFailed = EmailLog::whereIn('campaign_id', $campaignIds)
            ->whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED, EmailLog::STATUS_INVALID])
            ->count();

        $openRate = $emailsSent > 0 ? round(($emailsOpened / $emailsSent) * 100, 1) : 0;
        $clickRate = $emailsSent > 0 ? round(($emailsClicked / $emailsSent) * 100, 1) : 0;
        $deliveryRate = $emailsSent > 0 ? round(($emailsDelivered / $emailsSent) * 100, 1) : 0;

        $contactsImported = (int) $this->importLogs()->sum('imported');

        $lastCampaign = $this->campaigns()->latest()->first();
        $lastActivity = $lastCampaign?->updated_at ?? $this->updated_at;

        return [
            'total_campaigns' => $totalCampaigns,
            'sent_campaigns' => $sentCampaigns,
            'draft_campaigns' => $draftCampaigns,
            'in_progress_campaigns' => $inProgressCampaigns,
            'emails_sent' => $emailsSent,
            'emails_delivered' => $emailsDelivered,
            'emails_opened' => $emailsOpened,
            'emails_clicked' => $emailsClicked,
            'emails_failed' => $emailsFailed,
            'open_rate' => $openRate,
            'click_rate' => $clickRate,
            'delivery_rate' => $deliveryRate,
            'contacts_imported' => $contactsImported,
            'last_activity' => $lastActivity,
            'last_campaign' => $lastCampaign,
        ];
    }
}
