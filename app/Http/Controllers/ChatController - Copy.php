<?php

namespace App\Http\Controllers;

use App\Events\MessagePosted;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class ChatController extends Controller
{

    public function index()
    {

        return view('app');
    }

    public function user_list(Request $request)
    {
        $users = User::latest()->where('id', '!=', auth()->user()->id)->take(5)->get();
        if ($request->ajax()) {
            return response()->json($users, 200);
        }
        return abort(404);
    }

    public function user_message($id, Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }

        $user = User::findOrFail($id);
        $messages = $this->message_by_user_id($id);

        return response()->json([
            'messages' => $messages,
            'user' => $user
        ]);
    }

    public function fetch_messages()
    {
        $messages = Chat::with('user')->get(); // adjust the pagination as needed

        return response()->json($messages);
    }

    public function send_message(Request $request)
    {
        $user = Auth::user();
        $messages = Chat::create([
            'message' => $request->message,
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->user_id
        ]);

        broadcast(new MessagePosted($messages));
        return response()->json($messages, 200);
    }

    public function delete_single_message($id, Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }

        Chat::findOrFail($id)->delete();
        return response()->json('delete message successfully', 200);
    }

    public function delete_all_message($id)
    {
        $messages = $this->message_by_user_id($id);
        foreach ($messages as $value) {
            Chat::findOrFail($value->id)->delete();
        }
        return response()->json('delete message successfully', 200);
    }


    public function search_user()
    {
        $searchQuery = request('query');
        $users = User::where('id', '!=', auth()->id())->get();
        return response()->json($users, $searchQuery ? 200 : 200);
    }

    public function message_by_user_id($id)
    {
        $message = Chat::where(function ($q) use ($id) {
            $q->where('sender_id', auth()->user()->id);
            $q->where('receiver_id', $id);
        })->orWhere(function ($q) use ($id) {
            $q->where('sender_id', $id);
            $q->where('receiver_id', auth()->user()->id);
        })->with('user')->get();

        return $message;
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'successfully logged out'
        ], 200);
    }
}
