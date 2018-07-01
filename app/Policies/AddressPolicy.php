<?php

namespace app\Policies;

use app\Models\Address;
use app\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Address $address)
    {
        return $user->id === $address->user_id;
    }
}
