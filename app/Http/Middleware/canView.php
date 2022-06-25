<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Section;

class canView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $section, $action)
    {
            if(auth()->user()->auth == '1') return $next($request);
            else {
                $section  = Section::where('name', $section)->first();
                $role = Role::where([['section_id', $section->id],['user_id', auth()->user()->id],['action', $action]])->first();
                if($role != null) return $next($request);;
            }
            return redirect('/');
    }
}
