<?php

namespace App\Http\Controllers;

use App\Models\Requests;
use App\Models\requestsOut;
use App\Models\Equipment;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;
use App\Notifications\TelegramNotifications;

class RequestsController extends Controller
{
      public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:requests,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            'out',
            'editOut',
            'outUpdate',
            'doneUpdate'
            ]
        ]);
        $this->middleware("canView:requests,read", [
        'only' => [
            'index' ,
            'requestsData',
            'requestOut',
            'outData',
            'done'
            ]
        ]);
        $this->middleware("canView:requests,delete", [
        'only' => [
            'destroy' 
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
        return view('requests.report');
    } 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $equipments =  DB::table('equipments')
                ->select('*')
                ->where('is_delete',0)
                ->get();

        $requestsOut =  DB::table('requestsOut')
                ->select('*')
                ->where('is_delete',0)
                ->where('status',0)
                ->get();

        $vessels =  DB::table('vessels')
                ->select('*')
                ->where('is_delete',0)
                ->where('status',0)
                ->get();

            $equips = array();
            foreach ($requestsOut as $requestOut) {
                $request = Requests::find($requestOut->request_id);
                array_push($equips,$request->equipment);
            }
            foreach ($vessels as $vessel) {
                    $equips1 =  explode("-",$vessel->equipment);
                    foreach ($equips1 as $equip ) {
                        $equipment = Equipment::find($equip);
                        array_push($equips,$equipment->id);
                    }
            }
            foreach ($equipments as  $key => $equipment ) {
                if(in_array($equipment->id, $equips)){
                    unset($equipments[$key]);
                }
            }
        
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $employees =  DB::table('hr.data')
                ->select('*')
                ->where('status',0)
                ->get();
        
        return view('requests.create',[
            'equipments' =>$equipments,
            'companies' =>$companies,
            'employees' =>$employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request,  TelegramNotifications  $telegram)
    {
        $validatedData = $request->validate([
            'equipment' => ['required'],
            'duration' => ['required'],
            'reason' => ['required'],
            'company' => ['required'],
            'employee' => ['required'],
            ],
            [
                'equipment.required' => '  ادخل       اسم   المُـــعده    ',
                'duration.required' => '  ادخل  مدة التشغيل المتوقعة    ',
                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'reason.required' => '  ادخل   الغرض من الطلب     ',
                'employee.required' => '  ادخل    اسم الطالب   ',
                
            ]);
            $requests = new Requests;
    
            $requests->equipment = $request->equipment;
            $requests->duration = $request->duration;
            $requests->company = $request->company;
            $requests->reason = $request->reason;
            $requests->employee = $request->employee;
            $requests->notes = $request->notes;
            $requests->save();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
            $employee = DB::table('hr.data')->select('*')->where('id' ,$request->employee)->first();

            $title = 'تم   إضافة طلب خروج مُعِــــده جديد  ';
            $body =  '  تم إضافة طلب خروج مُعِــــده جديد    ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipment->name;$body.= "\r\n /";
            $body .=  '    مدة التشغيل المتوقعة  '.$request->duration;$body.= "\r\n /";
            $body .=  '     الغرض من الطلب    '.$request->reason;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   اسم الطالب  '.$employee->name;$body.= "\r\n /";

            $message =  'تم إضافة طلب خروج '.$equipment->name;$message.= "\r\n";
            $message .=  'مدة التشغيل المتوقعة '.$request->duration.' ساعه';$message.= "\r\n";
            $message .=  'الغرض من الطلب '.$request->reason;$message.= "\r\n";
            $message .=  'الشركة التابعة لها '.$company->name_ar;$message.= "\r\n";
            $message .=  'اسم الطالب '.$employee->name;$message.= "\r\n";

            $request->session()->flash('NewRequest', $title);
            $telegram->send($message,'operation');
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/requests', 'action');

            event(new Notifications($title));
           return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $request = Requests::find($id);
         $locations =  DB::table('locations')
                ->select('*')
                ->get();
        $drivers =  DB::table('hr.data')
                ->select('*')
                ->wherein('job',[8,9,10,12,13,57,66,67,68,69,70,71,115,124,125,126,127,128,129,173])
                ->get();
        $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
        return view('requests.show',[
            'request'=>$request,
            'equipment'=>$equipment,
            'locations' =>$locations,
            'drivers' =>$drivers
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $requests = Requests::find($id);    

        $equipments =  DB::table('equipments')
                ->select('*')
                ->where('is_delete',0)
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $employees =  DB::table('hr.data')
                ->select('*')
                ->where('status',0)
                ->get();
        
        return view('requests.edit',[
            'requests' => $requests,
            'equipments' =>$equipments,
            'companies' =>$companies,
            'employees' =>$employees
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requests = Requests::find($id);    

        $validatedData = $request->validate([
            'equipment' => ['required'],
            'duration' => ['required'],
            'reason' => ['required'],
            'company' => ['required'],
            'employee' => ['required'],
            ],
            [
                'equipment.required' => '  ادخل       اسم   المُـــعده    ',
                'duration.required' => '  ادخل  مدة التشغيل المتوقعة    ',
                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'reason.required' => '  ادخل   الغرض من الطلب     ',
                'employee.required' => '  ادخل    اسم الطالب   ',
                
            ]);
    
            $body = '';
              if($requests->equipment != $request->equipment) {
                $equipment1 = DB::table('equipments')->select('*')->where('id' ,$requests->equipment)->first();
                $equipment2 = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
                $body .=  '  تم تغيير اسم المُـــعده  من ' . $equipment1->name. ' الى '.$equipment2->name;
                $body.= "\r\n /";
            } 
            if($requests->duration != $request->duration){
                    $body .=  '  تم تغيير مدة التشغيل المتوقعة  من '.$requests->duration. ' الى '.$request->duration;
                    $body.= "\r\n /";
            }   
            if($requests->company != $request->company) {
                $company1 = DB::table('hr.companies')->select('*')->where('id' ,$requests->company)->first();
                $company2 = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                $body .=  '  تم تغيير   الشركة التابعة لها  من ' . $company1->name_ar. ' الى '.$company2->name_ar;
                $body.= "\r\n /";
            }
            if($requests->reason != $request->reason) {
                $body .=  '  تم تغيير الغرض من الطلب  من ' . $requests->reason. ' الى '.$request->reason;
                $body.= "\r\n /";
            }
            if($requests->employee != $request->employee) {
                $employee1 = DB::table('hr.data')->select('*')->where('id' ,$requests->employee)->first();
                $employee2 = DB::table('hr.data')->select('*')->where('id' ,$request->employee)->first();
                $body .=  '  تم تغيير اسم الطالب  من ' . $employee1->name. ' الى '.$employee2->name;
                $body.= "\r\n /";
            }
            $requests->equipment = $request->equipment;
            $requests->duration = $request->duration;
            $requests->company = $request->company;
            $requests->reason = $request->reason;
            $requests->employee = $request->employee;
            $requests->notes = $request->notes;
            $requests->save();

            $title = 'تم   تعديل طلب خروج مُعِــــده  ';

            $request->session()->flash('EditRequest', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/requests', 'action');

            event(new Notifications($title));
            return back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
            $requests = Requests::find($id);    
            $requests->is_delete  = 1;
            $requests->save();

            $title = 'تم حذف طلب خروج مُعِــــده بنجاح';

            $auth = new AuthController();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$requests->equipment)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$requests->company)->first();
            $employee = DB::table('hr.data')->select('*')->where('id' ,$requests->employee)->first();

            $title = 'تم   حذف طلب خروج مُعِــــده جديد  ';
            $body =  '  تم حذف طلب خروج مُعِــــده جديد    ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipment->name;$body.= "\r\n /";
            $body .=  '    مدة التشغيل المتوقعة  '.$requests->duration;$body.= "\r\n /";
            $body .=  '     الغرض من الطلب    '.$requests->reason;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   اسم الطالب  '.$employee->name;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/requests', 'action');

            event(new Notifications($title));
    }
        public function requestsData()
    {
            $requests = Requests::where([['is_delete',0], ['status', 0]])->orderby('created_at','desc')->get();
            

            foreach ($requests as  $request) {
                    $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                    $employee = DB::table('hr.data')->select('*')->where('id' ,$request->employee)->first();

                    $request->equipment   = $equipment->name;
                    $request->company   = $company->name_ar;
                    $request->employee   = $employee->name;
                    $request->duration   = $request->duration. ' ساعة ';
                }

                    
            return DataTables::of($requests) 
                    ->addColumn('action', function($request) {
                        $action = '';
                    $auth = new AuthController();
                    if($auth->canView('requests', 'write')){
                        $action .='<a  href="/requests/' . $request->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canView('requests', 'delete')){
                        $action .='<a  coords="' . $request->equipment . '"  id="' . $request->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })
                ->addColumn('action2', function($request) {
                        $action2 = '';
                    $auth = new AuthController();
                    $equipment = DB::table('equipments')->select('*')->where('name' ,$request->equipment)->first();
                    if($equipment->conditions ==1){
                    if($auth->canView('requests', 'write')){
                        $action2 .='<a  href="/requests/' . $request->id . '" class="edit-button"><i class="fas fa-sign-out"></i> </a>';
                    }
                }else{
                            $action2 .='المُـــعده تعمل حاليا ';
                }
                    return $action2;       
                         })
                    ->addColumn('action3', function($request) {
                        $action3 = '';
                    $auth = new AuthController();
                    $equipment = DB::table('equipments')->select('*')->where('name' ,$request->equipment)->first();
                    if($equipment->conditions ==1){
                    if($auth->canView('requests', 'write')){
                        $action3 .='<a  href="/requests/print/' . $request->id . '" class="edit-button"><i class="fas fa-print"></i> </a>';
                    }
                }else{
                            $action3 .='المُـــعده تعمل حاليا ';
                }
                    return $action3;       
                         })
                ->escapeColumns(['action2' => 'action2'])
                ->escapeColumns(['action3' => 'action3'])
                ->rawColumns(['action2'])
                ->rawColumns(['action3'])
                ->make(true);
            
    }
       public function out(Request $request,  TelegramNotifications  $telegram)
    {
        $requests = Requests::find($request->request_id);    
        $equipments = Equipment::find($requests->equipment);    
        if($requests->status == 1)   header( "refresh:3;url=/requests" ); 

        $validatedData = $request->validate([
            'request_id' => ['required', 'max:255' , 'unique:requestsOut'],
            'driver' => ['required'],
            'details' => ['required'],
            'location' => ['required'],
            ],
            [
                'request_id.unique' => 'تم خروج الطلب من قبل',
                'request_id.required' => ' اعد المحاولة مرة أخرى ',

                'driver.required' => '  ادخل      اسم السائق    ',
                'details.required' => '  ادخل  ملاحظات الفحص قبل التحرك    ',
                'location.required' => '  ادخل   الموقع     ',

                
            ]);

            $requestsOut = new requestsOut;
    
            $requestsOut->request_id =$request->request_id;
            $requestsOut->driver =  join("-",$request->driver);
            $requestsOut->details = $request->details;
            $requestsOut->location = $request->location;
            $requestsOut->notes = $request->notes;
            $requestsOut->save();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$requests->equipment)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$request->location)->first();

            $drivers = '';
            foreach ($request->driver as $driver) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$driver)->first();
                $drivers .= $emp->name .' - ';
            }

            $title = 'تم عمل  تصريح خروج مُـــعده   ';
            $body =  '  تم عمل  تصريح خروج مُـــعده       ';$body.= "\r\n /";
            $body .=  'اسم  المعدة '.$equipment->name;$body.= "\r\n /";
            $body .=  'اسم السائق '.$drivers;$body.= "\r\n /";
            $body .=  ' ملاحظات الفحص قبل التحرك  '.$request->details;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";
            
            $message =  ' تم عمل  تصريح خروج ل '.$equipment->name;$message.= "\r\n";
            $message .=  'اسم السائق '.$drivers;$message.= "\r\n";
            $message .=  'ملاحظات الفحص قبل التحرك '.$request->details;$message.= "\r\n";
            $message .=  'موقع العمل '.$location->name;$message.= "\r\n";


            $requests->status = 1;
            $requests->save();
            
            $equipments->conditions  = 2;
            $equipments->save();

            // $request->session()->flash('OutRequest', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/requests', 'action');

            event(new Notifications($title));
            $telegram->send($message,'operation');
            return redirect('requests')->with('OutRequest',$title);
            // return  back();
    }
    public function requestOut()
    {
        return view('requests.out');
    }
        public function outData()
    {
            $requests = Requests::where([['is_delete',0], ['status', 1]])->orderby('created_at','desc')->get();

            


            foreach ($requests as  $request) {
                    $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                    $employee = DB::table('hr.data')->select('*')->where('id' ,$request->employee)->first();

                    $request->equipment   = $equipment->name;
                    $request->company   = $company->name_ar;
                    $request->employee   = $employee->name;
                    $request->duration   = $request->duration. ' ساعة ';
                }

                    
            return DataTables::of($requests) 
                    ->addColumn('action', function($request) {
                        $action = '';
                    $auth = new AuthController();
                    $requestsOut = DB::table('requestsOut')->select('*')->where([['request_id',$request->id]])->first();
                     if($requestsOut->status == 0 ){
                    if($auth->canView('requests', 'write')){
                        $action .='<a  href="/requests/' . $request->id . '/editOut" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                }else{
                    $action .='<a  href="/requests/printOut/' . $request->id . '" class="edit-button"><i class="fas fa-print"></i> </a>';
                }
                    return $action;
                })
                ->addColumn('action2', function($request) {
                    $requestsOut = DB::table('requestsOut')->select('*')->where([['request_id',$request->id]])->first();
                    $action2 = '';
                    $auth = new AuthController();
                    if($requestsOut->status == 0 ){
                    if($auth->canView('requests', 'write')){
                        $action2 .='<a  href="/requests/done/' . $request->id . '" class="edit-button"><i class="fas fa-sign-out"></i> </a>';
                    }
                }else {
                        $action2.=' تم رجوع المُـــعده' ;
                    }
                    return $action2;            
                    })
                ->escapeColumns(['action2' => 'action2'])
                ->rawColumns(['action2'])
                ->make(true);
            
    }
        public function editOut($id)
    {
        $request = Requests::find($id);
        $locations =  DB::table('locations')->select('*')->get();
        $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
        $requestsOut = DB::table('requestsOut')->select('*')->where([['request_id',$id]])->first();

        return view('requests.editOut',[
            'requestsOut'=>$requestsOut,
            'equipment'=>$equipment,
            'locations' =>$locations
        ]);
        
    }
     public function outUpdate(Request $request, $id)
    {
       
        $requestsOut = requestsOut::find($id);

    $validatedData = $request->validate([
            'driver' => ['required'],
            'details' => ['required'],
            'location' => ['required'],
            ],
            [

                'driver.required' => '  ادخل      اسم السائق    ',
                'details.required' => '  ادخل  ملاحظات الفحص قبل التحرك    ',
                'location.required' => '  ادخل   الموقع     ',

                
            ]);
    
            $body = '';
            $requests = Requests::find($requestsOut->request_id);
            $equipment = DB::table('equipments')->select('*')->where('id' ,$requests->equipment)->first();
            $body .=  'اسم  المعدة '.$equipment->name;$body.= "\r\n /";

            if($requestsOut->driver != $request->driver){
                    $body .=  '  تم تغيير اسم السائق    من '.$requestsOut->driver. ' الى '.$request->driver;
                    $body.= "\r\n /";
            }   
            if($requestsOut->details != $request->details) {
                $body .=  '  تم تغيير ملاحظات الفحص قبل التحرك    من ' . $requestsOut->details. ' الى '.$request->details;
                $body.= "\r\n /";
            }
            if($requestsOut->location != $request->location) {
                $location1 = DB::table('locations')->select('*')->where('id' ,$requestsOut->location)->first();
                $location2 = DB::table('locations')->select('*')->where('id' ,$request->location)->first();
                $body .=  '  تم تغيير الموقع   من ' . $location1->name. ' الى '.$location2->name;
                $body.= "\r\n /";
            }

            $requestsOut->driver = $request->driver;
            $requestsOut->details = $request->details;
            $requestsOut->location = $request->location;
            $requestsOut->notes = $request->notes;
            $requestsOut->save();


            $title = 'تم تعديل  تصريح خروج مُـــعده   ';

           
            $request->session()->flash('OutEditRequest', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/requests', 'action');

            event(new Notifications($title));
            return  back();
    }

        public function done($id)
    {
        $request = Requests::find($id);
        $locations =  DB::table('locations')->select('*')->get();
        $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
        $requestsOut = DB::table('requestsOut')->select('*')->where([['request_id',$id]])->first();

        $emps = explode("-",$requestsOut->driver);
        $drivers = '';
            foreach ($emps as $driver) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$driver)->first();
                $drivers .= $emp->name .' - ';
            }
            
        return view('requests.done',[
            'requestsOut'=>$requestsOut,
            'equipment'=>$equipment,
            'drivers'=>$drivers,
            'locations' =>$locations
        ]);
        
    }
       public function doneUpdate(Request $request, $id,  TelegramNotifications  $telegram)
    {
        $requestsOut = requestsOut::find($id);    
        $requests = Requests::find($requestsOut->request_id);    
        $equipments = Equipment::find($requests->equipment);    

        $validatedData = $request->validate([
            'after_details' => ['required'],
            ],
            [
                'after_details.required' => '  ادخل  ملاحظات الفحص بعد  الإنتهاء    ', 
            ]);

    
            $requestsOut->status = 1;
            $requestsOut->after_details =$request->after_details;
            $requestsOut->after_notes = $request->after_notes;
            $requestsOut->save();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$requests->equipment)->first();

            $title = 'تم  رجوع  مُـــعده   ';
            $body =  '  تم رجوع    مُـــعده       ';$body.= "\r\n /";
            $body .=  'اسم  المعدة '.$equipment->name;$body.= "\r\n /";
            $body .=  ' ملاحظات الفحص بعد الإنتهاء  '.$request->after_details;$body.= "\r\n /";

            $equipments->conditions  = 1;
            $equipments->save();

            $message =  'تم رجوع '.$equipment->name;$message.= "\r\n";
            $message .=  'ملاحظات الفحص بعد الإنتهاء '.$request->after_details;$message.= "\r\n";


           
            // $request->session()->flash('OutRequest', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/requests', 'action');

            event(new Notifications($title));
            $telegram->send($message,'operation');
            return redirect('requests')->with('requestOut',$title);
            // return  back();
    }
    public function print($id){
        $request = Requests::find($id);    

        $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
        $employee = DB::table('hr.data')->select('*')->where('id' ,$request->employee)->first();

        $request->equipment = $equipment->name;
        $request->employee = $employee->name;

        return view('requests.print',[
            'request' => $request
        ]);
    } 
        public function printOut($id){
        $requestOut = requestsOut::find($id);    
        $request= Requests::find($requestOut->request_id);    

        $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
        $location = DB::table('locations')->select('*')->where('id' ,$requestOut->location)->first();
        $emps = explode("-",$requestOut->driver);
        $drivers = '';
            foreach ($emps as $driver) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$driver)->first();
                $drivers .= $emp->name .' - ';
            }
        $request->equipment = $equipment->name;
        $requestOut->location = $location->name;
        $requestOut->driver = rtrim($drivers, ' - ');

        return view('requests.printOut',[
            'request' => $request,
            'requestOut' => $requestOut
        ]);
    } 

}
