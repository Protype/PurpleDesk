<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'description',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['logo_url'];

    /**
     * 取得組織 Logo 完整 URL
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }
        
        return asset('storage/' . $this->avatar);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
