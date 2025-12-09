<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'notes',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Role checking methods
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'operator']);
    }

    /**
     * Status checking methods
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    /**
     * Permission checking methods
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canManageSessions(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canOperatePhotobox(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'operator']);
    }

    public function canViewReports(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canManagePackages(): bool
    {
        return $this->isAdmin();
    }

    public function canManageSettings(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Scope for filtering users by role
     */
    public function scopeStaff($query)
    {
        return $query->whereIn('role', ['admin', 'manager', 'operator']);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get formatted role name
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'operator' => 'Operator',
            'customer' => 'Customer',
            default => 'Unknown'
        };
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'bg-red-100 text-red-800',
            'manager' => 'bg-blue-100 text-blue-800',
            'operator' => 'bg-green-100 text-green-800',
            'customer' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-yellow-100 text-yellow-800',
            'banned' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Badge helper methods for views
     */
    public function getRoleText(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager', 
            'operator' => 'Operator',
            'customer' => 'Customer',
            default => 'Unknown'
        };
    }

    public function getRoleBadgeClass(): string
    {
        return match($this->role) {
            'admin' => 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800',
            'manager' => 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
            'operator' => 'px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800',
            'customer' => 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800',
            default => 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'banned' => 'Dinonaktifkan',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'active' => 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
            'inactive' => 'px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800',
            'banned' => 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800',
            default => 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
        };
    }

    /**
     * Relationships
     */
    public function photoSessions()
    {
        return $this->hasMany(PhotoSession::class, 'user_id');
    }
}
