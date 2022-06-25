<?php

namespace App\Models;

use RuntimeException;
use Illuminate\Hashing\HashManager;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use DB;

class Authenticator
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Hashing\HashManager
     */
    protected $hasher;

    /**
     * Create a new repository instance.
     *
     * @param  \Illuminate\Hashing\HashManager  $hasher
     * @return void
     */
    public function __construct(HashManager $hasher)
    {
        $this->hasher = $hasher->driver();
    }

    /**
     * This will attempt for login
     *
     * @param string $username UserName
     * @param string $password Password
     * @param string $provider Provider
     *
     * @return Authenticatable|null
     */
    public function attempt(
        string $username,
        string $password,
        string $provider
    ): ?Authenticatable {
        if (! $model = config('auth.providers.'.$provider.'.model')) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        /** @var Authenticatable $user */
        if (! $user = (new $model)->Where(
            [
                ['email', $username]
            ]
        )->first()
        ) {
            return null;
        }
       
        if (! $this->hasher->check($password, $user->getAuthPassword())) {
            return null;
        }
        // $user->last_login = Carbon::now()->format('Y-m-d H:m:s');
        // $user->save();

        return $user;
    }

    /**
     * This will attempt for virtuallogin
     *
     * @param string $userId   UserId
     * @param string $provider Provider
     *
     * @return Authenticatable|null
     */
    public function virtualAttempt(
        string $userId,
        string $provider = 'users'
    ): ?Authenticatable {
        if (! $model = config('auth.providers.'.$provider.'.model')) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        /** @var Authenticatable $user */
        if (! $user = (new $model)->Where(
            [['id', $userId],['is_deleted', 0]]
        )->first()
        ) {
            return null;
        }
        return $user;
    }
}
