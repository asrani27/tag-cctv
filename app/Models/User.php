<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';

    public const ROLE_USER = 'user';

    public const ROLE_KELURAHAN = 'kelurahan';

    public const DEFAULT_KELURAHAN_PASSWORD = 'kelurahanbanjarmasin';

    protected ?bool $hasDefaultPasswordCache = null;

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
            'is_active' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    /**
     * Determine if the user is a superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    /**
     * Determine if the user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Determine if the user has kelurahan role.
     */
    public function isKelurahan(): bool
    {
        return $this->role === self::ROLE_KELURAHAN;
    }

    /**
     * Extract kelurahan name if the user is a kelurahan account.
     */
    public function getKelurahanName(): ?string
    {
        if (! $this->isKelurahan()) {
            return null;
        }

        if (str_starts_with($this->name, 'Kelurahan ')) {
            return trim(substr($this->name, 10));
        }

        return $this->name;
    }

    /**
     * Determine if a kelurahan user still uses the default seeded password.
     */
    public function hasDefaultPassword(): bool
    {
        if (! $this->isKelurahan()) {
            return false;
        }

        if ($this->hasDefaultPasswordCache !== null) {
            return $this->hasDefaultPasswordCache;
        }

        if ($this->password_changed_at !== null) {
            return $this->hasDefaultPasswordCache = false;
        }

        return $this->hasDefaultPasswordCache = Hash::check(self::DEFAULT_KELURAHAN_PASSWORD, (string) $this->password);
    }

    /**
     * Clear the default password memoization cache.
     */
    public function resetDefaultPasswordCache(): void
    {
        $this->hasDefaultPasswordCache = null;
    }

    /**
     * Determine if the user account is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Get the surveys created by the user.
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(SurveyLocation::class, 'user_id');
    }

    /**
     * Get the usulans created by the user.
     */
    public function usulans(): HasMany
    {
        return $this->hasMany(Usulan::class, 'user_id');
    }

    /**
     * Get the photos uploaded by the user.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(SurveyPhoto::class, 'user_id');
    }
}
