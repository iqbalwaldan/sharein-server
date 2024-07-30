<?php

namespace App\Http\Controllers\Client\User;

use App\Events\ReminderEmailEvent;
use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reminders = Reminder::where('user_id', auth()->id())->latest();
            // dd($reminders->get());
            return DataTables::eloquent($reminders)
                ->addIndexColumn()
                ->editColumn('is_reminder', function ($reminder) {
                    return $reminder->is_reminder ? 'Yes' : 'No';
                })
                ->addColumn('action', 'client.user.reminder.action')
                ->toJson();
        }

        $user = auth()->user();
        $profilePhoto = $user->getFirstMediaUrl('profile') ?: '/assets/icons/profile-user.png';

        return view('client.user.reminder.index', [
            'title' => 'Reminder',
            'active' => 'reminder',
            'profilePhoto' => $profilePhoto,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reminder_time' => 'required|date',
            'name' => 'required|string',
            'email' => 'required|email',
            'description' => 'required|string',
        ]);
        Reminder::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description,
            'reminder_time' => $request->reminder_time,
        ]);
        return redirect()->route('user.reminder.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'reminder_time' => 'required|date',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        $reminder = Reminder::find($id);
        $reminder->update($request->all());
        return redirect()->route('user.reminder.index');
    }

    public function sendReminder()
    {
        $reminders = Reminder::where('reminder_time', '<=', now())->where('is_reminder', false)->get();
        foreach ($reminders as $reminder) {
            event(new ReminderEmailEvent($reminder)); //Sending Verification Email
            $reminder->update([
                'is_reminder' => true
            ]);
        }
    }

    public function destroy($id)
    {
        // dd($id);
        $reminder = Reminder::whereHas('schedules', function ($query) use ($id) {
            $query->where('reminder_id', $id);
        });
        if ($reminder->count() > 0) {
            return response()->json([
                'message' => 'Sorry, the reminder  cannot be deleted because it is still in use.',
            ], 400);
        } else {
            Reminder::find($id)->delete();
            return response()->json([
                'message' => 'You have succesfully update reminder.',
            ], 200);
        }
    }
}
