<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'reminder_id',
        'post_time',
        'is_posted',
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

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }

    public static function runScheduledUpdate()
    {
        $now = Carbon::now();
        // $schedules = self::where('post_time', '==', $now)
        //     ->whereHas('post', function ($query) {
        //         $query->where('status', 'scheduled');
        //     })
        //     ->get();

        // foreach ($schedules as $schedule) {
        //     $schedule->update([
        //         'is_posted' => 1,
        //     ]);
        // }
        // Schedule::where('id', 1)->update(['is_posted' => true]);
        $datas = Schedule::whereHas('post', function ($query) {
            $query->where('status', 'scheduled');
        })->get();

        // data post
        $dataPost = $datas[0]->post;

        // data media
        $dataMedia = $dataPost->getFirstMediaUrl('post_photo');

        dd($dataMedia);

        foreach ($datas as $data) {
            $data->update(['is_posted' => true]);
        }
    }

    public static function runScheduledPosts()
    {
        $now = Carbon::now();
        $schedules = Schedule::where('post_time', $now)->whereHas('post', function ($query) {
            $query->where('status', 'scheduled');
        })->get();

        foreach ($schedules as $schedule) {
            $post = $schedule->post;
            $page = json_decode($post->page->facebook_page);

            if ($post->media) {
                $response = Http::withToken($page->access_token)
                    ->post("https://graph.facebook.com/v20.0/{$page->id}/photos", [
                        'message' => $post->caption,
                        'url' => $post->getFirstMediaUrl('post_photo'),
                    ]);
            } else {
                $response = Http::withToken($page->access_token)
                    ->post("https://graph.facebook.com/v20.0/{$page->id}/feed", [
                        'message' => $post->caption,
                    ]);
            }

            $responseBody = $response->json();

            if (isset($responseBody['id']) || isset($responseBody['post_id'])) {
                $post->update([
                    'status' => 'published',
                    'post_id' => $responseBody['id'] ?? $responseBody['post_id'],
                    'media_id' => $responseBody['id'] ?? null,
                ]);
            }

            // $schedule->delete();
        }
    }
}
