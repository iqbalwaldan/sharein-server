<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class Reminder extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'email',
        'description',
        'reminder_time',
        'is_reminder',
    ];

    public function getCreatedAtAttribute()
    {
        if (!is_null($this->attributes["created_at"])) {
            return Carbon::parse($this->attributes["created_at"])->format(
                "Y-m-d H:i:s"
            );
        }
    }

    public function getUpdatedAtAttribute()
    {
        if (!is_null($this->attributes["updated_at"])) {
            return Carbon::parse($this->attributes["updated_at"])->format(
                "Y-m-d H:i:s"
            );
        }
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'description' => $this->description,
            'reminder_time' => $this->reminder_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reminder')
            ->useDisk('public')
            ->singleFile()
            ->withResponsiveImages();
    }

    public function schedules()
    {
        return $this->hasOne(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
