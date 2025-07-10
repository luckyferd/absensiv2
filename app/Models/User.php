<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
       'name',
        'email',
        'password',
        'role_id',
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
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }

    public function absensiGuru()
    {
        return $this->hasMany(AbsensiGuru::class, 'guru_id');
    }

    public function absensiMurid()
    {
        return $this->hasMany(AbsensiMurid::class, 'murid_id');
    }

    public function suratIzin()
    {
        return $this->hasMany(SuratIzin::class, 'murid_id');
    }
    public function getRoleNameAttribute()
    {
    return $this->role->name ?? null;
    }
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
}
