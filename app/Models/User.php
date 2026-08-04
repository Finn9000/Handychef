<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'age', 'address'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Role constants — avoids typos scattered across the codebase.
     */
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_GHOST_KITCHEN = 'ghost_kitchen';
    public const ROLE_ADMIN = 'admin';

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

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isGhostKitchen(): bool
    {
        return $this->role === self::ROLE_GHOST_KITCHEN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * The ghost kitchen profile owned by this user (if role = ghost_kitchen).
     */
    public function ghostKitchen()
    {
        return $this->hasOne(GhostKitchen::class);
    }

    /**
     * The subscriptions this user (customer) holds.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
