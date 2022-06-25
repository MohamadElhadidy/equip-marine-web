<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;


class BuildingController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:buildings,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            ]
        ]);
        $this->middleware("canView:buildings,read", [
        'only' => [
            'index' ,
            'buildingsData'
            ]
        ]);
        $this->middleware("canView:buildings,delete", [
        'only' => [
             'destroy' ,
            'restore',
            'trash' ,
            'buildingsTrash'
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
        return view('buildings.report');
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

        
        return view('buildings.create',[
            'companies' =>$companies
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
            'ownership' => ['required'],
            'ownership_date' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المنشآة   موجود  ',
                'code.required' => '  ادخل    كـــود المنشآة   ',

                'name.unique' => ' إســـــم المنشآة    موجود  ',
                'name.required' => ' ادخل إســـــم المنشآة ',

                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'ownership.required' => '  ادخل    الملكية   ',
                'ownership_date.required' => '  ادخل    تاريخ التعاقد   ',
                
            ]);
            $building = new Building;
    
            $building->code =$request->code;
            $building->name = $request->name;
            $building->company = $request->company;
            $building->location = $request->location;
            $building->ownership = $request->ownership;
            $building->ownership_date = $request->ownership_date;
            $building->notes = $request->notes;
            $building->save();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();

            $title = 'تم   إضافة منشآة جديدة ';
            $body =  '  تم إضافة منشآة جديدة  كود  '.$request->code;$body.= "\r\n /";
            $body .=  'اسم   '.$request->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$request->location;$body.= "\r\n /";
            $body .=  '   الملكية  '.$request->ownership;$body.= "\r\n /";

            $request->session()->flash('NewBuilding', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/buildings', 'action');

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
    public function edit(Building $building)
    {

        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();

        return view('buildings.edit',[
            'building' => $building,
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
    public function update(Request $request, Building $building)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:locations,code,'.$building->id],
            'name' => ['required','max:255',  'unique:locations,name,'.$building->id],
            'company' => ['required'],
            'location' => ['required'],
            'ownership' => ['required'],
            'ownership_date' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المنشآة   موجود  ',
                'code.required' => '  ادخل    كـــود المنشآة   ',

                'name.unique' => ' إســـــم المنشآة    موجود  ',
                'name.required' => ' ادخل إســـــم المنشآة ',

                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'ownership.required' => '  ادخل    الملكية   ',
                'ownership_date.required' => '  ادخل    تاريخ التعاقد   ',
                
            ]);
            $body = '';
            if($building->code != $request->code){
                    $body .=  '  تم تغيير كود المنشآة من '.$building->code. ' الى '.$request->code;
                    $body.= "\r\n /";
            }   
            if($building->name != $request->name) {
                $body .=  '  تم تغيير اسم المنشآة من ' . $building->name. ' الى '.$request->name;
                $body.= "\r\n /";
            }
            if($building->company != $request->company) {
                $company1 = DB::table('hr.companies')->select('*')->where('id' ,$building->company)->first();
                $company2 = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                $body .=  '  تم تغيير   الشركة التابعة لها  من ' . $company1->name_ar. ' الى '.$company2->name_ar;
                $body.= "\r\n /";
            }
            if($building->location != $request->location) {
                $body .=  '  تم تغيير الموقع  من ' . $building->location. ' الى '.$request->location;
                $body.= "\r\n /";
            }
            if($building->ownership != $request->ownership) {
                $body .=  '  تم تغيير  الملكية من ' . $building->ownership. ' الى '.$request->ownership;
                $body.= "\r\n /";
            }
            if($building->ownership_date != $request->ownership_date) {
                $body .=  '  تم تغيير تاريخ التعاقد من ' . $building->ownership_date. ' الى '.$request->ownership_date;
                $body.= "\r\n /";
            }
 
            $building->code =$request->code;
            $building->name = $request->name;
            $building->company = $request->company;
            $building->location = $request->location;
            $building->ownership = $request->ownership;
            $building->ownership_date = $request->ownership_date;
            $building->notes = $request->notes;
            $building->save();


            $title = 'تم تعديل المنشآة بنجاح';


            $request->session()->flash('EditBuilding', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/buildings', 'action');

        event(new Notifications($title));

        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        
        
        return  back()->with([
            'Building' => $building,
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
            $building = Building::find($id);    
            $building->is_delete  = 1;
            $building->save();

            $title = 'تم حذف المنشآة بنجاح';

            $auth = new AuthController();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$building->company)->first();

            $title = 'تم   حذف منشآة  ';
            $body =  '  تم حذف منشآة   كود  '.$building->code;$body.= "\r\n /";
            $body .=  'اسم   '.$building->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$building->location;$body.= "\r\n /";
            $body .=  '   الملكية  '.$building->ownership;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/buildings', 'action');

            event(new Notifications($title));

    }
   
    public function buildingsData()
    {
            $buildings = Building::where('is_delete',0)->get();
            

            foreach ($buildings as  $building) {
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$building->company)->first();
                    $building->company   = $company->name_ar;
                }
                
            return DataTables::of($buildings) 
                ->addColumn('action', function ($building) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canview('buildings', 'write')){
                        $action .='<a  href="/buildings/' . $building->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canview('buildings', 'delete')){
                        $action .='<a  coords="' . $building->name . '"  id="' . $building->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }


    public function trash()
    {
        return view('buildings.trash');
    }
    
        public function buildingsTrash()
    {
            $buildings = Building::where('is_delete',1)->get();
            
            foreach ($buildings as  $building) {
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$building->company)->first();
                    $building->company   = $company->name_ar;
                }
                
            return DataTables::of($buildings) 
                ->addColumn('action', function ($building) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canview('buildings', 'delete')){
                        $action .='<a  coords="' . $building->name . '"  id="' . $building->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function restore($id)
    {

            $building = Building::find($id);    
            $building->is_delete  = 0;
            $building->save();

            $title = 'تم استرجاع المنشآة بنجاح';

            $auth = new AuthController();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$building->company)->first();

            $title = 'تم   استرجاع منشآة  ';
            $body =  '  تم استرجاع منشآة   كود  '.$building->code;$body.= "\r\n /";
            $body .=  'اسم   '.$building->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$building->location;$body.= "\r\n /";
            $body .=  '   الملكية  '.$building->ownership;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/buildings', 'action');

            event(new Notifications($title));
    }

}
