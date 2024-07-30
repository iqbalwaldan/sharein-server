<?php

namespace App\Http\Controllers\Client\User;

use App\Events\ReminderEmailEvent;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reminder;
use App\Models\Schedule;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoPostController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profilePhoto = $user->getFirstMediaUrl('profile') ?: '/assets/icons/profile-user.png';

        return view('client.user.auto-post.index', [
            'title' => 'Auto Post',
            'active' => 'auto-post',
            'profilePhoto' => $profilePhoto,
        ]);
    }

    // Async
    public function store(Request $request)
    {
        $facebook_page =  json_decode($request->facebook_page);
        $promises = [];
        $client = new Client();

        foreach ($facebook_page as $page) {
            $decode = json_decode($page);

            // Save post to database
            DB::beginTransaction();
            $post = Post::create([
                'user_id' => auth()->id(),
                'page_id' => $decode->id,
                'caption' => $request->caption,
                'status' => 'publish',
                'page_token' => $decode->page_access_token,
            ]);

            // Save media to database
            if ($request->hasFile('file_input')) {
                $file = $request->file('file_input');
                $fileCopy = $file->storeAs('tmp', $file->getClientOriginalName());

                $post->addMedia(storage_path('app/tmp/' . $file->getClientOriginalName()))
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('post_photo', 'media/post');
            }

            DB::commit();

            // Save schedule to database
            if ($request->has('datetime')) {
                // Save to reminder
                $reminder = Reminder::create([
                    'user_id' => auth()->id(),
                    'name' =>  $decode->name . ' Reminder',
                    'email' => auth()->user()->email,
                    'description' => 'Reminder for shceduled post on ' . $decode->name . ' page',
                    'reminder_time' => $request->datetime,
                ]);
                // Save to schedule
                Schedule::create([
                    'post_id' => $post->id,
                    'reminder_id' => $reminder->id,
                    'post_time' => $request->datetime,
                ]);

                $post->update([
                    'status' => 'scheduled',
                ]);
            } else {
                if ($request->hasFile('file_input')) {
                    // Post Image to Facebook
                    $promises[] = $client->postAsync('https://graph.facebook.com/v20.0/' . $decode->id . '/photos', [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $decode->page_access_token,
                        ],
                        'form_params' => [
                            'message' => $request->caption,
                            'url' => $post->getFirstMediaUrl('post_photo'),
                        ]
                    ])->then(
                        function ($response) use ($post) {
                            $response = json_decode($response->getBody(), true);
                            $post->update([
                                'post_id' => $response['post_id'],
                                'media_id' => $response['id'],
                            ]);
                        },
                        function ($exception) {
                            // Handle exception or log error
                        }
                    );
                } else {
                    // Post Caption to Facebook
                    $promises[] = $client->postAsync('https://graph.facebook.com/v20.0/' . $decode->id . '/feed', [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $decode->page_access_token,
                        ],
                        'form_params' => [
                            'message' => $request->caption,
                        ]
                    ])->then(
                        function ($response) use ($post) {
                            $response = json_decode($response->getBody(), true);
                            $post->update([
                                'post_id' => $response['id'],
                            ]);
                        },
                        function ($exception) {
                            // Handle exception or log error
                        }
                    );
                }
            }
        }

        // Wait for all requests to complete
        Utils::settle($promises)->wait();
        return redirect()->route('user.auto-post.index');
    }

    public function schedulePost()
    {
        DB::beginTransaction();

        $posts = Post::where('status', 'scheduled')->whereHas('schedules', function ($query) {
            $query->where('post_time', '<=', now());
        })->get();

        // $facebook_pages = DashboardController::getFacebookData();
        $client = new Client();
        $promises = [];

        foreach ($posts as $post) {
            // foreach ($facebook_pages['data'] as $page) {
            // if ($page['id'] == $post->page_id) {
            if ($post->getFirstMediaUrl('post_photo')) {
                // Post Image to Facebook
                $promises[] = $client->postAsync('https://graph.facebook.com/v20.0/' . $post->page_id . '/photos', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $post->page_token,
                    ],
                    'form_params' => [
                        'message' => $post->caption,
                        'url' => $post->getFirstMediaUrl('post_photo'),
                    ]
                ])->then(
                    function ($response) use ($post) {
                        $response = json_decode($response->getBody(), true);
                        $post->update([
                            'post_id' => $response['post_id'],
                            'media_id' => $response['id'],
                            'status' => 'publish',
                        ]);
                    },
                    function ($exception) {
                        // Handle exception or log error
                    }
                );
            } else {
                // Post Caption to Facebook
                $promises[] = $client->postAsync('https://graph.facebook.com/v20.0/' . $post->page_id . '/feed', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $post->page_token,
                    ],
                    'form_params' => [
                        'message' => $post->caption,
                    ]
                ])->then(
                    function ($response) use ($post) {
                        $response = json_decode($response->getBody(), true);
                        $post->update([
                            'post_id' => $response['id'],
                            'status' => 'publish',
                        ]);
                    },
                    function ($exception) {
                        // Handle exception or log error
                    }
                );
            }
            // }
            // }
        }

        // Wait for all requests to complete
        Utils::settle($promises)->wait();

        DB::commit();
    }

    // Sync

    // public function post(Request $request)
    // {
    //     $facebook_page = $request->facebook_page;
    //     for ($i = 0; $i < count($facebook_page); $i++) {
    //         $decode = json_decode($facebook_page[$i]);

    //         // Save post to database
    //         DB::beginTransaction();
    //         $post = Post::create([
    //             'user_id' => auth()->id(),
    //             'page_id' => $decode->id,
    //             'caption' => $request->caption,
    //             'status' => 'publish',
    //         ]);

    //         // Save media to database
    //         if ($request->hasFile('file_input')) {
    //             $file = $request->file('file_input');
    //             $fileCopy = $file->storeAs('tmp', $file->getClientOriginalName());

    //             $post->addMedia(storage_path('app/tmp/' . $file->getClientOriginalName()))
    //                 ->usingFileName($file->getClientOriginalName())
    //                 ->toMediaCollection('post_photo', 'media/post');
    //         }

    //         DB::commit();
    //         // Save schedule to database
    //         if ($request->has('date')) {
    //             $datetime = $request->date . ' ' . $request->time;
    //             $schedule = Schedule::create([
    //                 'post_id' => $post->id,
    //                 'post_time' => $datetime,
    //             ]);

    //             $post->update([
    //                 'status' => 'scheduled',
    //             ]);

    //             // Save to reminder
    //             $reminder = Reminder::create([
    //                 'user_id' => auth()->id(),
    //                 'name' => 'Post Reminder',
    //                 'email' => auth()->user()->email,
    //                 'description' => 'Post Reminder',
    //                 'reminder_time' => $datetime,
    //             ]);
    //         } else {
    //             if ($request->hasFile('file_input')) {
    //                 // Post Image to Facebook
    //                 $client = new Client();
    //                 $response = $client->request('POST', 'https://graph.facebook.com/v20.0/' . $decode->id . '/photos', [
    //                     'headers' => [
    //                         'Accept' => 'application/json',
    //                         'Authorization' => 'Bearer ' . $decode->access_token,
    //                     ],
    //                     'form_params' => [
    //                         'message' => $request->caption,
    //                         'url' => $post->getFirstMediaUrl('post_photo'),
    //                     ]
    //                 ]);
    //             } else {
    //                 // Post Caption to Facebook
    //                 $client = new Client();
    //                 $response = $client->request('POST', 'https://graph.facebook.com/v20.0/' . $decode->id . '/feed', [
    //                     'headers' => [
    //                         'Accept' => 'application/json',
    //                         'Authorization' => 'Bearer ' . $decode->access_token,
    //                     ],
    //                     'form_params' => [
    //                         'message' => $request->caption,
    //                     ]
    //                 ]);
    //             }
    //             $response = json_decode($response->getBody(), true);
    //             if (isset($response['post_id'])) {
    //                 $post->update([
    //                     'post_id' => $response['post_id'],
    //                     'media_id' => $response['id'],
    //                 ]);
    //             } else if (isset($response['id'])) {
    //                 $post->update([
    //                     'post_id' => $response['id'],
    //                 ]);
    //             }
    //         }
    //     }
    // }

    // public function schedulePost()
    // {
    //     DB::beginTransaction();
    //     $posts = Post::where('status', 'scheduled')->whereHas('schedules', function ($query) {
    //         $query->where('post_time', '<=', now());
    //     })->get();
    //     $facebook_pages = self::getFacebookData()->original;

    //     foreach ($posts as $post) {
    //         foreach ($facebook_pages as $page) {
    //             if ($page['id'] == $post->page_id) {
    //                 if ($post->getFirstMediaUrl('post_photo')) {
    //                     // Post Image to Facebook
    //                     $client = new Client();
    //                     $response = $client->request('POST', 'https://graph.facebook.com/v20.0/' . $page['id'] . '/photos', [
    //                         'headers' => [
    //                             'Accept' => 'application/json',
    //                             'Authorization' => 'Bearer ' . $page['access_token'],
    //                         ],
    //                         'form_params' => [
    //                             'message' => $post->caption,
    //                             'url' => $post->getFirstMediaUrl('post_photo'),
    //                         ]
    //                     ]);
    //                     $response = json_decode($response->getBody(), true);
    //                     $post->update([
    //                         'post_id' => $response['post_id'],
    //                         'media_id' => $response['id'],
    //                         'status' => 'publish',
    //                     ]);
    //                 } else {
    //                     // Post Caption to Facebook
    //                     $client = new Client();
    //                     $response = $client->request('POST', 'https://graph.facebook.com/v20.0/' . $page['id'] . '/feed', [
    //                         'headers' => [
    //                             'Accept' => 'application/json',
    //                             'Authorization' => 'Bearer ' . $page['access_token'],
    //                         ],
    //                         'form_params' => [
    //                             'message' => $post->caption,
    //                         ]
    //                     ]);
    //                     $response = json_decode($response->getBody(), true);
    //                     $post->update([
    //                         'post_id' => $response['id'],
    //                         'status' => 'publish',
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    //     DB::commit();
    // }
}
