<?php

namespace App\Http\Controllers\Client\User;

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Controller;
use App\Models\FacebookAccount;
use Facebook\Facebook;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FacebookAccountController extends Controller
{
    public function index(Request $request)
    {
        // $base64Data = $_COOKIE['facebookData'];
        // $jsonData = base64_decode($base64Data);
        // $data = json_decode($jsonData, true);
        // dd($data);

        $user = auth()->user();
        $profilePhoto = $user->getFirstMediaUrl('profile') ?: '/assets/icons/profile-user.png';

        if ($request->ajax()) {
            $facebook = FacebookAccount::where('user_id', auth()->id())->latest();
            return DataTables::of($facebook)
                ->addIndexColumn()
                ->addColumn('action', 'client.user.facebook-account.action')
                ->toJson();
        };

        return view('client.user.facebook-account.index', [
            'title' => 'Facebook Account',
            'active' => 'facebook-account',
            'profilePhoto' => $profilePhoto,

        ]);
    }

    public function store(Request $request)
    {
        DashboardController::addFacebookAccount($request);
        // check cookie
        self::updateCookies();
    }

    public function update(Request $request, $id)
    {
        $data = $request->user_access_token;
        $client = new Client();
        $responseAccount = $client->request('GET', 'https://graph.facebook.com/v20.0/me', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $data, // Jika memerlukan token
            ]
        ]);
        $dataAccount = json_decode($responseAccount->getBody(), true);
        DB::beginTransaction();
        FacebookAccount::where('id', $id)->update([
            'user_id' => auth()->user()->id,
            'facebook_id' => $dataAccount['id'],
            'facebook_name' => $dataAccount['name'],
            'user_access_token' => $data,
        ]);
        DB::commit();
    }

    public function destroy($id)
    {
        // dd($id);
        $facebook = FacebookAccount::find($id);
        $facebook->delete();
        // check cookie
        self::updateCookies();
    }

    public static function updateCookies()
    {
        $data = FacebookAccount::where('user_id', auth()->id())->get();
        if ($data->isEmpty()) {
            Cookie::queue(Cookie::forget('facebookData'));
        } else {
            // Set cookie
            setcookie('facebookData', DashboardController::fetchFacebookData(), time() + (86400 * 30), "/"); // Cookie will expire in 30 days
        }
    }
}
