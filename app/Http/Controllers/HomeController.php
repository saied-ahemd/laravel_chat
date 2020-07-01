<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Message;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $users=User::where('id','!=' ,  Auth::id() )->get();
        $users= DB::select("SELECT users.id,users.name, users.avatar,users.email, count(is_read) as unread 
        from users LEFT JOIN messages ON user.id = messages.from and is_read=0  and  messages.to = " .Auth::id(). "
        where users.id!=" .Auth::id(). " group by users.id, users.name, users.avatar,users.email");

        return view('home',['users'=>$users]);
    }
    public function getmasseges($user_id){
        $my_id= Auth::id();
        //get message depend on user id
        //and those message $from=Auth::id() and $to = $user_id or $from = $user_id  and $to =Auth::id() =>
        $messeges= Message::where(function ($query) use ($my_id,$user_id) {
            $query->where('from',$my_id)->where('to',$user_id);
        })->orWhere(function ($query) use ($my_id,$user_id) {
            $query->where('from',$user_id)->where('to',$my_id);
        })->get();

        return view('masseges.index',['messeges'=>$messeges]);
    }
    public function sendmessage(Request $request){
        $from=Auth::id();
        $to=$request->rec_id;
        $message=$request->message;
        $data= new Message();
        $data->from= $from;
        $data->to= $to;
        $data->message= $message;
        $data->is_read= 0;
        $data->save();

        //pusher
        $options = [
            'cluster' => 'ap2',
            'useTLS' => true
        ];

        $pusher= new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data=['from'=> $from ,'to'=> $to]; //send from and to user when press enter
        $pusher->trigger('my-channel','my-event',$data);
        
        
    }
}
