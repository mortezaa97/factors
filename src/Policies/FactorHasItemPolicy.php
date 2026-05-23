<?php

namespace Mortezaa97\Factors\Policies;

use Mortezaa97\Factors\Models\FactorHasItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FactorHasItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FactorHasItem $factorHasItem): bool
    {
        return $user->id === $factorHasItem->created_by || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FactorHasItem $factorHasItem): bool
    {
        return $user->id === $factorHasItem->created_by || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FactorHasItem $factorHasItem): bool
    {
        return $user->id === $factorHasItem->created_by || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FactorHasItem $factorHasItem): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FactorHasItem $factorHasItem): bool
    {
        return $user->hasRole('admin');
    }
}

