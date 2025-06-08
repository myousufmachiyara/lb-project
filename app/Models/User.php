<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles {
        HasRoles::hasPermissionTo as protected originalHasPermissionTo;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->hasRole('superadmin')) {
            return true; // superadmin has all permissions
        }

        return $this->originalHasPermissionTo($permission, $guardName);
    }
}