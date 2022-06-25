<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;


class WorkshopController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:workshops,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            ]
        ]);
        $this->middleware("canView:workshops,read", [
        'only' => [
            'index' ,
            'workshopsData'
            ]
        ]);
        $this->middleware("canView:workshops,delete", [
        'only' => [
            'destroy' ,
            'restore',
            'trash' ,
            'workshopsTrash'
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
        return view('workshops.report');
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
        $departments =  DB::table('hr.departments')
                ->select('*')
                ->where('company','1')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();
        
        return view('workshops.create',[
            'departments' =>$departments,
            'companies' =>$companies,
            'locations' =>$locations
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
            'code' => ['required', 'max:255' , 'unique:workshops'],
            'name' => ['required','max:255', 'unique:workshops'],
            'company' => ['required'],
            'location' => ['required'],
            'department' => ['required'],
            ],
            [
                'code.unique' => '   كـــود الورشة الداخلية   موجود  ',
                'code.required' => '  ادخل    كـــود الورشة الداخلية   ',

                'name.unique' => ' إســـــم الورشة الداخلية    موجود  ',
                'name.required' => ' ادخل إســـــم الورشة الداخلية ',

                'company.required' => '  ادخل   الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'department.required' => '    ادخل     الإدارة التابعة لها       ',
                
            ]);

            $workshop = new Workshop;
    
            $workshop->code =$request->code;
            $workshop->name = $request->name;
            $workshop->company = $request->company;
            $workshop->location = $request->location;
            $workshop->department = $request->department;
            $workshop->notes = $request->notes;
            $workshop->save();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
            $department = DB::table('hr.departments')->select('*')->where('id' ,$request->department)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$request->location)->first();

            $title = 'تم   إضافة ورشة داخلية جديدة ';
            $body =  '  تم إضافة ورشة داخلية جديدة  كود  '.$request->code;$body.= "\r\n /";
            $body .=  'اسم   '.$request->name;$body.= "\r\n /";
            $body .=  '   الإدارة التابعة لها  '.$department->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";

            $request->session()->flash('NewWorkshop', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/workshops', 'action');

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
    public function edit(Workshop $workshop)
    {

        $departments =  DB::table('hr.departments')
                ->select('*')
                ->where('company','1')
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();
        
        return view('workshops.edit',[
            'workshop' => $workshop,
            'departments' =>$departments,
            'companies' =>$companies,
            'locations' =>$locations
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workshop $workshop)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:workshops,code,'.$workshop->id],
            'name' => ['required','max:255',  'unique:workshops,name,'.$workshop->id],
            'company' => ['required'],
            'location' => ['required'],
            'department' => ['required'],
            ],
            [
                'code.unique' => '   كـــود الورشة الداخلية   موجود  ',
                'code.required' => '  ادخل    كـــود الورشة الداخلية   ',

                'name.unique' => ' إســـــم الورشة الداخلية    موجود  ',
                'name.required' => ' ادخل إســـــم الورشة الداخلية ',

                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'department.required' => '  ادخل    الإدارة   التابعة  لها',
                
            ]);
            $body = '';
            if($workshop->code != $request->code){
                    $body .=  '  تم تغيير كود الورشة الداخلية من '.$workshop->code. ' الى '.$request->code;
                    $body.= "\r\n /";
            }   
            if($workshop->name != $request->name) {
                $body .=  '  تم تغيير اسم الورشة الداخلية من ' . $workshop->name. ' الى '.$request->name;
                $body.= "\r\n /";
            }
            if($workshop->company != $request->company) {
                $company1 = DB::table('hr.companies')->select('*')->where('id' ,$workshop->company)->first();
                $company2 = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                $body .=  '  تم تغيير   الشركة التابعة لها  من ' . $company1->name_ar. ' الى '.$company2->name_ar;
                $body.= "\r\n /";
            }
            if($workshop->department != $request->department) {
                $department1 = DB::table('hr.departments')->select('*')->where('id' ,$workshop->department)->first();
                $department2 = DB::table('hr.departments')->select('*')->where('id' ,$request->department)->first();
                $body .=  '  تم تغيير   الإدارة التابعة لها  من ' . $department1->name. ' الى '.$department2->name;
                $body.= "\r\n /";
            }
            if($workshop->location != $request->location) {
                $location1 = DB::table('locations')->select('*')->where('id' ,$workshop->location)->first();
                $location2 = DB::table('locations')->select('*')->where('id' ,$request->location)->first();
                $body .=  '  تم تغيير الموقع  من ' . $location1->name. ' الى '.$location2->name;
                $body.= "\r\n /";
            }
 
            $workshop->code =$request->code;
            $workshop->name = $request->name;
            $workshop->company = $request->company;
            $workshop->location = $request->location;
            $workshop->department = $request->department;
            $workshop->notes = $request->notes;
            $workshop->save();


            $title = 'تم تعديل الورشة الداخلية بنجاح';


            $request->session()->flash('EditWorkshop', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/workshops', 'action');

        event(new Notifications($title));

        $departments =  DB::table('hr.departments')
                ->select('*')
                ->where('company','1')
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();
        
        return  back()->with([
            'workshop' => $workshop,
            'departments' =>$departments,
            'companies' =>$companies,
            'locations' =>$locations
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
            $workshop = Workshop::find($id);    
            $workshop->is_delete  = 1;
            $workshop->save();

            $title = 'تم حذف الورشة الداخلية بنجاح';

            $auth = new AuthController();

            $department = DB::table('hr.departments')->select('*')->where('id' ,$workshop->department)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$workshop->company)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$workshop->location)->first();

            $title = 'تم   حذف ورشة داخلية  ';
            $body =  '  تم حذف ورشة داخلية   كود  '.$workshop->code;$body.= "\r\n /";
            $body .=  'اسم   '.$workshop->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";
            $body .=  ' الإدارة التابعة لها  '.$department->name;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/workshops', 'action');

            event(new Notifications($title));

    }
   
    public function workshopsData()
    {
            $workshops = Workshop::where('is_delete',0)->get();
            

            foreach ($workshops as  $workshop) {
                    $department = DB::table('hr.departments')->select('*')->where('id' ,$workshop->department)->first();
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$workshop->company)->first();
                    $location = DB::table('locations')->select('*')->where('id' ,$workshop->location)->first();

                    $workshop->department   = $department->name;
                    $workshop->company   = $company->name_ar;
                    $workshop->location   = $location->name;
                }
                
            return DataTables::of($workshops) 
                ->addColumn('action', function ($workshop) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('workshops', 'write')){
                        $action .='<a  href="/workshops/' . $workshop->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canView('workshops', 'delete')){
                        $action .='<a  coords="' . $workshop->name . '"  id="' . $workshop->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }



    public function trash()
    {
        return view('workshops.trash');
    }
    
        public function workshopsTrash()
    {            $workshops = Workshop::where('is_delete',1)->get();
            

            foreach ($workshops as  $workshop) {
                    $department = DB::table('hr.departments')->select('*')->where('id' ,$workshop->department)->first();
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$workshop->company)->first();
                    $location = DB::table('locations')->select('*')->where('id' ,$workshop->location)->first();

                    $workshop->department   = $department->name;
                    $workshop->company   = $company->name_ar;
                    $workshop->location   = $location->name;
                }
                
            return DataTables::of($workshops) 
                ->addColumn('action', function ($workshop) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('workshops', 'delete')){
                        $action .='<a  coords="' . $workshop->name . '"  id="' . $workshop->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function restore($id)
    {

            $workshop = Workshop::find($id);    
            $workshop->is_delete  = 0;
            $workshop->save();

            $title = 'تم استرجاع الورشة الداخلية بنجاح';

            $auth = new AuthController();

            $department = DB::table('hr.departments')->select('*')->where('id' ,$workshop->department)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$workshop->company)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$workshop->location)->first();

            $title = 'تم   استرجاع ورشة داخلية  ';
            $body =  '  تم استرجاع ورشة داخلية   كود  '.$workshop->code;$body.= "\r\n /";
            $body .=  'اسم   '.$workshop->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";
            $body .=  ' الإدارة التابعة لها  '.$department->name;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/workshops', 'action');

            event(new Notifications($title));

    }
}
