<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const ROLES = ['pengguna', 'Humas', 'Manajerial', 'Kepegawaian', 'Hukum', 'Visualisasi Data', 'Pengawasan Siaran'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'email_verified_at',
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
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Get the clients that the user has requested/been approved access for.
     */
    public function accessedClients()
    {
        return $this->belongsToMany(\Laravel\Passport\Client::class, 'client_user_access', 'user_id', 'client_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Relasi ke UserClientRole (HasMany)
     */
    public function clientRoles()
    {
        return $this->hasMany(UserClientRole::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    public function isStaff()
    {
        return in_array($this->role, self::ROLES);
    }
}
