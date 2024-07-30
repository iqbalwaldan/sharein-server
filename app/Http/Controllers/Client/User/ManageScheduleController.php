<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\DashboardController as FacebookData;
use App\Models\Post;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ManageScheduleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $schedule = Schedule::whereHas('post', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('status', "scheduled");
            })->with(['reminder', 'post', 'post.media'])->latest();

            // Data facebook
            $page_facebook = new FacebookData();
            $data_facebook = $page_facebook->getFacebookData();

            // Map Facebook data 
            $mappedDataPage = [];
            foreach ($data_facebook as $value) {
                $mappedDataPage[$value['id']] = $value['name'];
            }
            $mappedDataAccount = [];
            foreach ($data_facebook as $value) {
                $mappedDataAccount[$value['id']] = $value['facebook_name'];
            }

            // Get schedules and convert to array
            $schedules = $schedule->get()->toArray();

            // Add Facebook page name to each post in the schedules
            foreach ($schedules as &$scheduleItem) {
                $postId = $scheduleItem['post']['page_id'];
                if (isset($mappedDataPage[$postId])) {
                    $scheduleItem['post']['page_name'] = $mappedDataPage[$postId];
                    if (isset($mappedDataAccount[$postId])) {
                        $scheduleItem['post']['facebook_name'] = $mappedDataAccount[$postId];
                    } else {
                        $scheduleItem['post']['facebook_name'] = 'Unknown';
                    }
                } else {
                    $scheduleItem['post']['page_name'] = 'Unknown';
                }
            }
            return DataTables::of($schedules)
                ->addIndexColumn()
                ->addColumn('action', 'client.user.manage-schedule.action')
                ->toJson();
        }

        $user = auth()->user();
        $profilePhoto = $user->getFirstMediaUrl('profile') ?: '/assets/icons/profile-user.png';

        return view('client.user.manage-schedule.index', [
            'title' => 'Manage Schedule',
            'active' => 'manage-schedule',
            'profilePhoto' => $profilePhoto,
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($request->has('page_id')) {
            $request->validate([
                'page_id' => 'required',
            ]);
            $schedule = Schedule::find($id);
            $post = Post::find($schedule->post_id);

            $post->update([
                'page_id' => $request->page_id,
                'caption' => $request->caption,
            ]);

            if ($request->media != null) {
                // hapus media lama
                $post->media()->delete();
                // tambah media baru
                $file = $request->file('media');
                $post->addMedia($file)
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('post_photo', 'media/post');
            }
            return redirect()->route('user.manage-schedule.index');
        } else {
            $request->validate([
                'post_time' => 'required|date',
            ]);
            $schedule = Schedule::find($id);
            $reminder = $schedule->reminder;
            $reminder->update([
                'reminder_time' => $request->post_time,
            ]);
            $schedule->update($request->all());
        }
        return redirect()->route('user.manage-schedule.index');
    }

    public function destroy($id)
    {
        // dd($id);
        $schedule = Schedule::find($id);
        $reminder = $schedule->reminder;
        $schedule->delete();
        $reminder->delete();
    }
}
