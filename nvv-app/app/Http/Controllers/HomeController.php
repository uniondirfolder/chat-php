<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $users = DB::select("select users.id, users.name, users.avatar, users.email, count(is_read) as unread
        from users LEFT  JOIN  messages ON users.id = messages.from and is_read = 0 and messages.to = " . Auth::id() . "
        where users.id != " . Auth::id() . "
        group by users.id, users.name, users.avatar, users.email");
        return view('home', ['users' => $users]);
    }

    public function getMessage($user_id)
    {
        $my_id = Auth::id();


        DB::table('messages')->where(['from' => $user_id, 'to' => $my_id])->update(['is_read' => 1]);

        $messages = DB::table('messages')->where(function ($query) use ($user_id, $my_id) {
            $query->where('from', $user_id)->where('to', $my_id);
        })->orWhere(function ($query) use ($user_id, $my_id) {
            $query->where('from', $my_id)->where('to', $user_id);
        })->get();


        return view('messages.index', ['messages' => $messages]);
    }

    /**
     * @throws \Pusher\PusherException
     * @throws \Pusher\ApiErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendMessage(Request $request)
    {
        \Log::debug("Home-----------------");
        $from = Auth::id();
        $to = $request->receiver_id;
        $message = $request->message;

        $data = new Message();
        $data->from = $from;
        $data->to = $to;
        $data->message = $message;
        $data->is_read = 0; // message will be unread when sending message
        $data->save();

        // pusher
        $options = array(
            'cluster' => 'eu',
        );

        $pusher = new Pusher(
            'ae9344bdef72cf7e7fe1',
            '84d26632134cfef20c57',
            '1306766',
            $options
        );


        $data = ['from' => $from, 'to' => $to];
        $pusher->trigger('nvv-chanel', 'nvv-event', $data);
    }

}
