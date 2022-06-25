<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\Role;
use App\Models\Notification;
use App\Models\User;
use App\Models\Section;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    view()->composer('*', function ($view) 
    {
        if(Auth::user() != null){
            $user = User::find(Auth::user()->id);
            if($user->auth == '1') {
                $notifications = Notification::latest()->paginate(5);
            } 
            elseif ($user->auth == '2' ) {
                    $notifications = Notification::where('auth',2)
                        ->latest()
                        ->paginate(5);
            } 

            if(isset($notifications)) $view->with('notifications', $notifications );    
        }
    });  

    Blade::if('canView', function ($section, $action) {
            if(auth()->user()->auth == '1') return true;
            else {
                $section  = Section::where('name', $section)->first();
                if($section != null){
                $role = Role::where([['section_id', $section->id],['user_id', auth()->user()->id],['action', $action]])->first();
                if($role != null) return true;
                    }
            }
                return false;
        });
    
    }
}
