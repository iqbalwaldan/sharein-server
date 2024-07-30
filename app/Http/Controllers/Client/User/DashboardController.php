<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Controller;
use App\Models\FacebookAccount;
use App\Models\Post;
use App\Models\Reminder;
use App\Models\Schedule;
use Facebook\Facebook;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        // Check and set cookies
        $data = FacebookAccount::where('user_id', auth()->id())->get();
        if ($data->isEmpty()) {
            Cookie::queue(Cookie::forget('facebookData'));
        }
        if (!isset($_COOKIE['facebookData'])) {
            // Set cookie
            setcookie('facebookData', self::fetchFacebookData(), time() + (86400 * 30), "/"); // Cookie will expire in 30 days
        }

        // Total Schedule
        $totalSchedule = Schedule::whereHas('post', function ($query) {
            $query->where('user_id', auth()->id())->where('status', 'scheduled');
        })->count();

        // Total Reminder
        $totalReminder = Reminder::where('user_id', auth()->id())->where('is_reminder', 0)->count();

        // Total Account
        $totalAccount = FacebookAccount::where('user_id', auth()->id())->count();

        // Total Page
        $facebookCookies = self::getFacebookData();
        // dd($facebookCookies);
        $totalPage = count($facebookCookies);

        // Calendar events
        $userId = auth()->id();
        $scheduleData = DB::select("
            SELECT DATE(schedules.post_time) as `start`, CONCAT('Total Schedule : ', COUNT(schedules.id)) as title
            FROM schedules 
            INNER JOIN posts ON posts.id = schedules.post_id 
            WHERE posts.user_id = ?
            AND posts.status = 'scheduled'
            GROUP BY `start`
        ", [$userId]);

        $reminderData = DB::select("
            SELECT DATE(reminders.reminder_time) as `start`, CONCAT('Total Reminder : ', COUNT(reminders.id)) as title
            FROM reminders 
            WHERE user_id = ?
            AND is_reminder = 0
            GROUP BY `start`
        ", [$userId]);

        $calendarEvents = array_merge($scheduleData, $reminderData);

        // Facebook page list
        // $pageList = [];
        // foreach ($facebookCookies as $item) {
        //     $facebookId = $item['facebook_id'];
        //     if (!isset($pageList[$facebookId])) {
        //         $pageList[$facebookId] = [
        //             'facebook_id' => $facebookId,
        //             'facebook_name' => $item['facebook_name'],
        //             'pages' => []
        //         ];
        //     }

        //     $pageList[$facebookId]['pages'][] = [
        //         'id' => $item['id'],
        //         'name' => $item['name'],
        //         'page_access_token' => $item['page_access_token']
        //     ];
        // }

        // // Convert to indexed array if needed
        // $pageList = array_values($pageList);

        // $pageList = [
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        //     [
        //         "facebook_id" => "122145133118156724",
        //         "facebook_name" => "Sharein Kuyy",
        //         "page_access_token" => "a",
        //         "id" => "344715422054821",
        //         "name" => "ShareIn Info"
        //     ],
        // ];
        $pageList = $facebookCookies;

        $user = auth()->user();
        $profilePhoto = $user->getFirstMediaUrl('profile') ?: '/assets/icons/profile-user.png';

        return view('client.user.dashboard.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'totalSchedule' => $totalSchedule,
            'totalReminder' => $totalReminder,
            'totalAccount' => $totalAccount,
            'totalPage' => $totalPage,
            'calendarEvents' => $calendarEvents,
            'pageList' => $pageList,
            'profilePhoto' => $profilePhoto,
        ]);
    }

    public function schedule(Request $request)
    {
        if ($request->ajax()) {
            // Schedule
            $schedules = Schedule::whereHas('post', function ($query) {
                $query->where('user_id', auth()->id())->where('status', 'scheduled');
            })
                ->join('posts', 'posts.id', '=', 'schedules.post_id')
                ->orderBy('post_time', 'asc')->limit(5);

            $facebookCookies = self::getFacebookData();
            $mappedDataPage = [];
            foreach ($facebookCookies as $value) {
                $mappedDataPage[$value['id']] = $value['name'];
            }

            $schedules = $schedules->get()->toArray();

            foreach ($schedules as &$scheduleItem) {
                $postId = $scheduleItem['page_id'];
                if (isset($mappedDataPage[$postId])) {
                    $scheduleItem['page_name'] = $mappedDataPage[$postId];
                } else {
                    $scheduleItem['page_name'] = 'Unknown';
                }
            }
            return DataTables::of($schedules)
                ->addIndexColumn()
                // ->addColumn('action', 'client.user.dashboard.action')
                ->toJson();
        }
    }

    public function reminder(Request $request)
    {
        if ($request->ajax()) {
            // Reminder
            $reminders = Reminder::where('user_id', auth()->id())
                ->orderBy('reminder_time', 'asc')->limit(5);

            $reminders = $reminders->get()->toArray();
            // dd($reminders);

            return DataTables::of($reminders)
                ->addIndexColumn()
                // ->addColumn('action', 'client.user.dashboard.action')
                ->toJson();
        }
    }

    public static function addFacebookAccount($user_access_token)
    {
        $data = $user_access_token;
        $client = new Client();
        $responseAccount = $client->request('GET', 'https://graph.facebook.com/v20.0/me', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $data, // Jika memerlukan token
            ]
        ]);
        $dataAccount = json_decode($responseAccount->getBody(), true);
        DB::beginTransaction();
        FacebookAccount::create([
            'user_id' => auth()->user()->id,
            'facebook_id' => $dataAccount['id'],
            'facebook_name' => $dataAccount['name'],
            'user_access_token' => $data,
        ]);
        DB::commit();
    }

    public static function fetchFacebookData()
    {
        $facebook = FacebookAccount::where('user_id', auth()->id())->get();
        $facebookData = [];
        foreach ($facebook as $key => $value) {
            $client = new Client();
            $response = $client->request('GET', 'https://graph.facebook.com/v20.0/me/accounts', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $value->user_access_token, // Jika memerlukan token
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            // Buat maping data
            foreach ($data['data'] as $v) {
                $facebookData[] = [
                    'facebook_id' => $value->facebook_id,
                    'facebook_name' => $value->facebook_name,
                    'page_access_token' => $v['access_token'],
                    'id' => $v['id'],
                    'name' => $v['name'],
                ];
            }
        }

        // Encode data
        $dataEncode = json_encode($facebookData);
        // Encode data to base64
        // $base64Data = base64_encode($dataEncode);
        // return $base64Data;
        return $dataEncode;
    }

    public static function getFacebookData()
    {
        if (isset($_COOKIE['facebookData'])) {
            // $base64Data = $_COOKIE['facebookData'];
            // $jsonData = base64_decode($base64Data);
            // $data = json_decode($jsonData, true);
            // return $data;
            $jsonData = $_COOKIE['facebookData'];
            $data = json_decode($jsonData, true);
            return $data;
        }
        return [];
    }
}
