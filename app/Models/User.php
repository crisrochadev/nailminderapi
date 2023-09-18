<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserPage;
use App\Models\Image;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'address_cep',
        'address_street',
        'address_number',
        'address_district',
        'address_complement',
        'address_city',
        'address_state',
        'slug'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function providers()
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(Image::class, 'user_id', 'id');
    }
    public function page()
    {
        return $this->hasMany(UserPage::class, 'user_id', 'id');
    }
}
