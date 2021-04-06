<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * @return LengthAwarePaginator
     */
    public function index():LengthAwarePaginator
    {
        return $users = User::with('address')->paginate(25);
    }

    /**
     * @param array $attributes
     *
     * @return User
     */
    public function create(array $attributes): User
    {
        // If details array is provided, it will be taken out from attributes.
        $address = Arr::pull($attributes, 'address');

        $attributes['password'] = bcrypt($attributes['password']);

        $user = User::Create($attributes);

        if ($address) {
            $user->address()->create(['address' => $address]);
            $user['address'] = $address;
        }

        return $user;
    }

    /**
     * @param User $user
     * @param array $attributes
     *
     * @return User
     */
    public function update(User $user, array $attributes): User
    {
        // If details array is provided, it will be taken out from attributes.
        $address = Arr::pull($attributes, 'address');

        $user->update($attributes);

        if ($address) {
            $user->address()->update(['address' => $address]);
            $user['address'] = $address;
        }

        return $user;
    }

    /**
     * @param User $user
     *
     * @return bool|null
     * @throws Exception
     */
    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
