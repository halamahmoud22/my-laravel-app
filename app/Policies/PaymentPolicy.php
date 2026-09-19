<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->id === $payment->order->user_id
            || $user->role === 'admin'
            || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Order $order): bool
    {
        return $user->id === $order->user_id
            || $user->role === 'admin'
            || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->role === 'admin' || $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {    // financial records - never deletable by anyone through the app
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payment $payment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        return false;
    }
}
