<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;


class LocationController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:locations,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            ]
        ]);
        $this->middleware("canView:locations,read", [
        'only' => [
            'index' ,
            'locationsData'
            ]
        ]);
        $this->middleware("canView:locations,delete", [
        'only' => [
             'destroy' ,
            'restore',
            'trash' ,
            'locationsTrash'
            ]
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('locations.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $types =  DB::table('types')
                ->select('*')
                ->get();

        
        return view('locations.create',[
            'companies' =>$companies,
            'types' =>$types
        ]);
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
            'code' => ['required', 'max:255' , 'unique:locations'],
            'name' => ['required','max:255', 'unique:locations'],
            'company' => ['required'],
            'location' => ['required'],
            'type' => ['required'],
            'ownership' => ['required'],
            'ownership_date' => ['required'],
            ],
            [
                'code.unique' => '   ??????????    ??????????  ',
                'code.required' => '  ????????    ??????????    ',

                'name.unique' => ' ????????????????     ??????????  ',
                'name.required' => ' ???????? ????????????????  ',

                'company.required' => '  ????????    ???????????? ??????????????    ',
                'location.required' => '  ????????   ????????????     ',
                'type.required' => '  ????????   ??????????     ',
                'ownership.required' => '  ????????    ??????????????   ',
                'ownership_date.required' => '  ????????    ?????????? ??????????????   ',
                
            ]);
            $location = new Location;
    
            $location->code =$request->code;
            $location->name = $request->name;
            $location->company = $request->company;
            $location->type = $request->type;
            $location->location = $request->location;
            $location->ownership = $request->ownership;
            $location->ownership_date = $request->ownership_date;
            if(isset($request->capacity))  $location->capacity = $request->capacity;
            if(isset($request->size))  $location->size = $request->size;
            $location->notes = $request->notes;
            $location->save();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
            $type = DB::table('types')->select('*')->where('id' ,$request->type)->first();

            $title = '????   ?????????? ???????? ?????? ';
            $body =  '  ???? ?????????? '.$type->name;$body.= "\r\n /";
            $body .=  '??????  '.$request->code;$body.= "\r\n /";
            $body .=  '??????   '.$request->name;$body.= "\r\n /";
            $body .=  ' ???????????? ?????????????? ??????  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   ????????????  '.$request->location;$body.= "\r\n /";
            $body .=  '   ??????????????  '.$request->ownership;$body.= "\r\n /";
            if(isset($request->capacity)) {$body .=  '   ?????????? ??????????  '.$request->capacity;$body.= "\r\n /";}
            if(isset($request->size)) {$body .=  '   ??????????????  '.$request->size;$body.= "\r\n /";}

            $request->session()->flash('NewLocation', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/locations', 'action');

            event(new Notifications($title));

            return  back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {

        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $types =  DB::table('types')
                ->select('*')
                ->get();

        return view('locations.edit',[
            'location' => $location,
            'types' => $types,
            'companies' =>$companies
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:locations,code,'.$location->id],
            'name' => ['required','max:255',  'unique:locations,name,'.$location->id],
            'company' => ['required'],
            'location' => ['required'],
            'type' => ['required'],
            'ownership' => ['required'],
            'ownership_date' => ['required'],
            ],
            [
                'code.unique' => '   ??????????    ??????????  ',
                'code.required' => '  ????????    ??????????    ',

                'name.unique' => ' ????????????????     ??????????  ',
                'name.required' => ' ???????? ????????????????  ',

                'company.required' => '  ????????    ???????????? ??????????????    ',
                'location.required' => '  ????????   ????????????     ',
                'type.required' => '  ????????   ??????????     ',
                'ownership.required' => '  ????????    ??????????????   ',
                'ownership_date.required' => '  ????????    ?????????? ??????????????   ',
                
            ]);
            $body = '';
            if($location->code != $request->code){
                    $body .=  '  ???? ?????????? ??????????  ???? '.$location->code. ' ?????? '.$request->code;
                    $body.= "\r\n /";
            }   
            if($location->name != $request->name) {
                $body .=  '  ???? ?????????? ????????????????  ???? ' . $location->name. ' ?????? '.$request->name;
                $body.= "\r\n /";
            }
            if($location->type != $request->type) {
                $type1 = DB::table('types')->select('*')->where('id' ,$location->type)->first();
                $type2 = DB::table('types')->select('*')->where('id' ,$request->type)->first();
                $body .=  '  ???? ?????????? ?????????? ???? ' . $type1->name. ' ?????? '.$type2->name;
                $body.= "\r\n /";
            }
            if($location->company != $request->company) {
                $company1 = DB::table('hr.companies')->select('*')->where('id' ,$location->company)->first();
                $company2 = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                $body .=  '  ???? ??????????   ???????????? ?????????????? ??????  ???? ' . $company1->name_ar. ' ?????? '.$company2->name_ar;
                $body.= "\r\n /";
            }
            if($location->location != $request->location) {
                $body .=  '  ???? ?????????? ????????????  ???? ' . $location->location. ' ?????? '.$request->location;
                $body.= "\r\n /";
            }
            if($location->ownership != $request->ownership) {
                $body .=  '  ???? ??????????  ?????????????? ???? ' . $location->ownership. ' ?????? '.$request->ownership;
                $body.= "\r\n /";
            }
            if($location->ownership_date != $request->ownership_date) {
                $body .=  '  ???? ?????????? ?????????? ?????????????? ???? ' . $location->ownership_date. ' ?????? '.$request->ownership_date;
                $body.= "\r\n /";
            }
            if(isset($request->capacity)){
             if($location->capacity != $request->capacity) {
                $body .=  '  ???? ??????????  ?????????? ??????????   ???? ' . $location->capacity. ' ?????? '.$request->capacity;
                $body.= "\r\n /";
            }}
            if(isset($request->size)){
            if($location->size != $request->size) {
                $body .=  '  ???? ?????????? ??????????????  ???? ' . $location->size. ' ?????? '.$request->size;
                $body.= "\r\n /";
            }}
 
            $location->code =$request->code;
            $location->name = $request->name;
            $location->type = $request->type;
            $location->company = $request->company;
            $location->location = $request->location;
            $location->ownership = $request->ownership;
            $location->ownership_date = $request->ownership_date;
            if(isset($request->capacity)){
                    $location->capacity = $request->capacity;
            } else {
                $location->capacity = null;
            } 
            if(isset($request->size)){
                    $location->size = $request->size;
            } else {
                $location->size = null;
            } 
            $location->notes = $request->notes;
            $location->save();


            $title = '???? ?????????? ???????????? ??????????';


            $request->session()->flash('EdiLocation', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/locations', 'action');

        event(new Notifications($title));

        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();

        $types =  DB::table('types')
                ->select('*')
                ->get();
        
        return  back()->with([
            'location' => $location,
            'types' => $types,
            'companies' =>$companies
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
            $location = Location::find($id);    
            $location->is_delete  = 1;
            $location->save();

            $title = '???? ?????? ???????????? ??????????';

            $auth = new AuthController();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$location->company)->first();
            $type = DB::table('types')->select('*')->where('id' ,$location->type)->first();

            $title = '????   ?????? ????????  ';
            $body =  '  ???? ?????? ????????   ??????  '.$location->code;$body.= "\r\n /";
            $body .=  '??????   '.$type->name;$body.= "\r\n /";
            $body .=  '??????   '.$location->name;$body.= "\r\n /";
            $body .=  ' ???????????? ?????????????? ??????  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   ????????????  '.$location->location;$body.= "\r\n /";
            $body .=  '   ??????????????  '.$location->ownership;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/locations', 'action');

            event(new Notifications($title));

    }
   
    public function locationsData()
    {
            $locations = Location::where('is_delete',0)->get();
            

            foreach ($locations as  $location) {
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$location->company)->first();
                    $location->company   = $company->name_ar;
                    $type = DB::table('types')->select('*')->where('id' ,$location->type)->first();
                    $location->type   = $type->name;
                }
                
            return DataTables::of($locations) 
                ->addColumn('action', function ($location) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canview('locations', 'write')){
                        $action .='<a  href="/locations/' . $location->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canview('locations', 'delete')){
                        $action .='<a  coords="' . $location->name . '"  id="' . $location->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }


    public function trash()
    {
        return view('locations.trash');
    }
    
        public function locationsTrash()
    {
            $locations = Location::where('is_delete',1)->get();
            
            foreach ($locations as  $location) {
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$location->company)->first();
                    $location->company   = $company->name_ar;
                    $type = DB::table('types')->select('*')->where('id' ,$location->type)->first();
                    $location->type   = $type->name;
                }
                
            return DataTables::of($locations) 
                ->addColumn('action', function ($location) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canview('locations', 'delete')){
                        $action .='<a  coords="' . $location->name . '"  id="' . $location->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function restore($id)
    {

            $location = Location::find($id);    
            $location->is_delete  = 0;
            $location->save();

            $title = '???? ?????????????? ???????????? ??????????';

            $auth = new AuthController();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$location->company)->first();
            $type = DB::table('types')->select('*')->where('id' ,$location->type)->first();

            $title = '????   ?????????????? ????????  ';
            $body =  '  ???? ?????????????? ????????   ??????  '.$location->code;$body.= "\r\n /";
            $body .=  '??????   '.$type->name;$body.= "\r\n /";
            $body .=  '??????   '.$location->name;$body.= "\r\n /";
            $body .=  ' ???????????? ?????????????? ??????  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   ????????????  '.$location->location;$body.= "\r\n /";
            $body .=  '   ??????????????  '.$location->ownership;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/locations', 'action');

            event(new Notifications($title));
    }

}
