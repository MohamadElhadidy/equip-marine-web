<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use DataTables;
use App\Models\Role;
use App\Models\Section;
use App\Events\Notifications;

class AuthController extends Controller
{

        public function __construct()
        {
        $this->middleware("canView:notifications,read", [
        'only' => [
            'notifications' ,
            ]
        ]);
    }
    public function login(Request $request)
    {
        $this->validate($request,[
            'username'=> ['required','exists:users,username'],
            'password'=>['required']
        ],
        [
                    'username.exists' => ' اسم المستخدم  غير موجود  ',
                    'username.required' =>  'ادخل اسم المستخدم',

                    'password.required' => 'ادخل  كلمة السر',
            ]);
        
    
        if(!auth()->attempt($request->only('username', 'password'),'on')){
            return back()->withErrors([
            'password' => 'كلمة السر غير صحيحة',
        ])->withInput($request->only('username','password'));
        }
        
        $auth = new AuthController();
        $title = 'تم تسجيل الدخول بنجاح';
        $body = $title;
        $auth->notify(auth()->user()->id, 1, $title, $body, '/users', 'move');
        event(new Notifications($title));

        return redirect('/');
    }

    public function logout(Request $request)
    {
        $auth = new AuthController();
        $title = 'تم تسجيل الخروج بنجاح';
        $body = $title;
        $auth->notify(auth()->user()->id, 1, $title, $body, '/users', 'move');
        event(new Notifications($title));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function notify($user_id, $auth, $title, $body, $url, $type)
    {
        if($user_id == 1 AND $type == 'move'){
                $i = 0;
        }else{
        $notification = new Notification;

        $notification->user_id =$user_id;
        $notification->auth = $auth;
        $notification->title = $title;
        $notification->body = $body;
        $notification->url = $url;
        $notification->type = $type;
        $notification->save();
        }
    }

    public function notifications()
    {
        return view('notifications');
    }

    public function notificationsData()
    {
            $user = User::find(Auth::user()->id);

            if($user->auth == '1') {
                $notifications = Notification::all();
            } 
            elseif ($user->auth == '2' ) {
                    $notifications = Notification::where('auth',2)->get();
            } 

            if(isset($notifications)){
                foreach ($notifications as  $notification) {
                    $notification->user_id   = $notification->user->name;
                }
                return DataTables::of($notifications)->make(true);
            } 
    }
    public function canView($section, $action)
    {
        if(auth()->user()->auth == '1') return true;
            else {
                $section  = Section::where('name', $section)->first();
                $role = Role::where([['section_id', $section->id],['user_id', auth()->user()->id],['action', $action]])->first();
                if($role != null) return true;
            }
            return false;
            
    }
}
