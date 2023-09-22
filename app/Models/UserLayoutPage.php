<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\DashItems;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class UserLayoutPage extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'name', 'thumbnail', 'url', 'page_id'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DashItems::class, 'layout_id', 'id');
    }
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'user_pages_id','id');
    }
}
