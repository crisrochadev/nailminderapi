<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserLayoutPage;

class UserPage extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'title', 'description', 'user_id', 'active', 'layout_id'
    ];
    public function layout(): HasOne
    {
        return $this->hasOne(UserLayoutPage::class, 'id', 'layout_id');
    }
    
}
