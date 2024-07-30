<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class FacebookAccount extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'user_id',
        'facebook_id',
        'facebook_name',
        // 'avatar_url',
        'user_access_token',
    ];

    public function getUpdatedAtAttribute()
    {
        if (!is_null($this->attributes["updated_at"])) {
            return Carbon::parse($this->attributes["updated_at"])->format(
                "Y-m-d H:i:s"
            );
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reminder')
            ->useDisk('public')
            ->singleFile()
            ->withResponsiveImages();
    }
}
