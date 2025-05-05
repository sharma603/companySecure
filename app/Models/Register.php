<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Register extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'registers';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The roles that belong to the register.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_register');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return (bool) $this->roles->pluck('name')->intersect($roles)->count();
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return (bool) $this->roles->pluck('name')->intersect($roles)->count();
    }

    /**
     * Check if the user has all of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        return count($roles) === $this->roles->pluck('name')->intersect($roles)->count();
    }

    /**
     * Check if the user has a specific permission through any of their roles.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionThrough(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        
        return false;
    }
}
