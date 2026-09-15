<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    /**
     * Perform pre-authorization checks.
     * Administrators have unrestricted access to all campaigns.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }

    /**
     * Determine whether the user can send the campaign.
     */
    public function send(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }

    /**
     * Determine whether the user can schedule or unschedule the campaign.
     */
    public function schedule(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }

    /**
     * Determine whether the user can cancel the campaign.
     */
    public function cancel(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }

    /**
     * Determine whether the user can retry failed emails for the campaign.
     */
    public function retry(User $user, Campaign $campaign): bool
    {
        return $campaign->isOwnedBy($user);
    }
}
