<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Section;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Events\Notifications;
use DB;
use DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     if(auth()->user()->auth == '1'){
            return view('users.report');
        }
        return view('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(auth()->user()->auth == '1'){
            $auth = DB::table('auth')->select('*')->get();
            return view('users.create',[
                'auth' => $auth
            ]);
        }
        return view('dashboard');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required','max:255', 'unique:users'],
            'username' => ['required', 'max:255' , 'unique:users'],
            'password' => ['required'],
            'auth' => ['required'],
            ],
            [
                'username.unique' => ' اسم المستخدم   موجود  ',
                'username.required' => '  ادخل اسم المستخدم    ',

                'name.unique' => ' الاسم    موجود  ',
                'name.required' => ' ادخل الاسم',

                'password.required' => ' ادخل كلمة السر',

                'auth.required' => ' ادخل نوع الحساب',
            ]);
            $user = new User;
            if($request->file != null ){
                $fileName = time().'_'.$request->file->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
                $user->image ='/storage/' .$filePath;
            }


            $user->name =$request->name;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->auth = $request->auth;
            $user->save();

            $title = 'تم إنشاء حساب بنجاح';
            $body =  '  تم انشاء حساب جديد  الاسم  '.$request->name;$body.= "\r\n /";
            $body .=  'اسم المستخدم  '.$request->username;$body.= "\r\n /";

            $request->session()->flash('newAccount', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id,1, $title, $body, '/users', 'action');

            event(new Notifications($title));

            return  back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where([['id', $id], ['is_delete', 0]])->first();    
        if($user != null){     
            if(auth()->user()->id != $id  AND auth()->user()->auth != '1' ){
                $id = auth()->user()->id;
                $user = User::find($id);
            }
        }else{
            $id = auth()->user()->id;
            $user = User::find($id);
        }
        if( !isset($user->auth)) return view('dashboard');
        $auth0 =  DB::table('auth')->select('*')->where('id', $user->auth)->first();
        $user->auth   = $auth0->name;

        $sections = Section::all();
        $auth = DB::table('auth')->select('*')->get();
        $roles = DB::table('roles')
                ->select('*')
                ->where('user_id' ,$id)
                ->groupBy('section_id')
                ->get();
        foreach ($roles as $role) {
            $section = Section::find($role->section_id);
            $roles2 = DB::table('roles')
                ->select('*')
                ->where('section_id' ,$role->section_id)
                ->where('user_id' ,$id)
                ->get();
                $actions = '';
                foreach ($roles2 as $role2) {
                    $actions .= $role2->action.' || ';
                }
            $role->section_id = $section->name_ar;
            $role->action = $actions;
        }
        return view('users.profile',[
            'user' => $user,
            'sections' => $sections,
            'roles' => $roles,
            'auth' => $auth
        ]);
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $user = User::where([['id', $id], ['is_delete', 0]])->first();
        $body = '';
        $validatedData = $request->validate([
            'name' => ['required','max:255', 'unique:users,name,'.$id],
            'username' => ['required', 'max:255' , 'unique:users,username,'.$id],
            ],
            [
                'username.unique' => ' اسم المستخدم   موجود  ',
                'username.required' => '  ادخل اسم المستخدم    ',

                'name.unique' => ' الاسم    موجود  ',
                'name.required' => ' ادخل الاسم',
            ]
        );
            if($request->file != null ){
                $fileName = time().'_'.$request->file->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
                $user->image ='/storage/' .$filePath;
                $body .=   ' تم تغيير الصورة الشخصية  '; $body.= "\r\n /"; 
            }
            if(!empty($request->password)) {
                $user->password = Hash::make($request->password);
                $body .= '  تم تغيير  الرقم السري  ';$body.= "\r\n /"; 
            }

            if($user->name != $request->name){
                    $body .=  '  تم تغيير الاسم من '.$user->name. ' الى '.$request->name;
                    $body.= "\r\n /";
            }   
            if($user->username != $request->username) {
                $body .=  '  تم تغيير اسم المستخدم من ' . $user->username. ' الى '.$request->username;
                $body.= "\r\n /";
            }
            $user->name = $request->name;
            $user->username = $request->username;


            if(isset($request->auth)) $user->auth = $request->auth;
            if($request->ignore == null){
            if(isset($request->section) AND isset($request->action)){
                $check = Role::where([['section_id', $request->section],['user_id', $id],['action', $request->action]])->first();
                
                if($check == null ){
                    $role = new Role;
                    $role->user_id =$id;
                    $role->section_id = $request->section;
                    $role->action = $request->action;
                    $role->save();
                }else{
                    $check->delete();
                }
            } 
        }
        
            
            
            $user->save();
            $title = 'تم تعديل الحساب بنجاح';


            $request->session()->flash('profile', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id,1, $title, $body, '/users', 'action');

            event(new Notifications($title));

            $sections = Section::all();
            $auth = DB::table('auth')->select('*')->get();
            $roles = DB::table('roles')
                ->select('*')
                ->where('user_id' ,$id)
                ->groupBy('section_id')
                ->get();
        foreach ($roles as $role) {
            $section = Section::find($role->section_id);
            $roles2 = DB::table('roles')
                ->select('*')
                ->where('section_id' ,$role->section_id)
                ->where('user_id' ,$id)
                ->get();
                $actions = '';
                foreach ($roles2 as $role2) {
                    $actions .= $role2->action.' || ';
                }
            $role->section_id = $section->name_ar;
            $role->action = $actions;
        }
        $auth0 =  DB::table('auth')->select('*')->where('id', $user->auth)->first();
        $user->auth   = $auth0->name;
        return  back()->with([
                'user' => $user,
                'sections' => $sections,
                'roles' => $roles,
                'auth' => $auth
            ]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);    
        $user->is_delete = 1;
        if($user->save()) DB::table('roles')->where('user_id', $id)->delete();
    }

    public function usersData()
    {
            $users = User::where([ ['is_delete', 0]])->get();
            foreach ($users as  $user) {
                    $auth =  DB::table('auth')->select('*')->where('id', $user->auth)->first();
                    $user->auth   = $auth->name;
                }
            return DataTables::of($users) 
                ->addColumn('action', function ($user) {
                    if($user->auth == 'admin' ){
                        return '<a  href="/users/' . $user->id . '" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }else{
                    return '<a  href="/users/' . $user->id . '" class="edit-button"><i class="fas fa-edit"></i> </a>
                    <a  coords="' . $user->name . '"  id="' . $user->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                   
            })->make(true);
            
    }
}
