<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Events\Notifications;
use DataTables;
use DB;
use App\Notifications\TelegramNotifications;

class MaintenanceController extends Controller
{

        public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:maintenance,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            'done'
            ]
        ]);
        $this->middleware("canView:maintenance,read", [
        'only' => [
            'index' ,
            'maintenanceData'
            ]
        ]);
        $this->middleware("canView:maintenance,delete", [
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
            return view('maintenance.report');
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
    
        return view('maintenance.create',[
            'equipments' =>$equipments
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
            'details' => ['required'],
            ],
            [
                'equipment.required' => '  ادخل       اسم   المُـــعده    ',
                'details.required' => '  ادخل    الأعمال التي يجب تنفيذها   ',
                
            ]);
            $maintenance = new Maintenance;
    
            $maintenance->equipment =$request->equipment;
            $maintenance->details = $request->details;
            $maintenance->notes = $request->notes;
            $maintenance->save();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();

            $title = ' تم   إضافة   طلب إصلاح مُعِــــده جديد  ';
            $body =  '  تم إضافة طلب إصلاح      ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipment->name;$body.= "\r\n /";
            $body .=  ' الأعمال التي يجب تنفيذها '.$request->details;$body.= "\r\n /";

            $message =  'تم إضافة طلب إصلاح '.$equipment->name;$message.= "\r\n";
            $message .=  'الأعمال التي يجب تنفيذها '.$request->details;$message.= "\r\n";

            $request->session()->flash('NewMaintenance', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/maintenance', 'action');

            event(new Notifications($title));
            $telegram->send($message, 'data');
            return back();
    }


   public function show($id)
    {
        $maintenance = Maintenance::find($id);
        $equipment = DB::table('equipments')->select('*')->where('id' ,$maintenance->equipment)->first();
        $employees =  DB::table('hr.data')
                ->select('*')
                ->get();
        return view('maintenance.show',[
            'maintenance' =>$maintenance,
            'equipment' =>$equipment,
            'employees' =>$employees,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
   public function edit($id)
    {
        $maintenance = Maintenance::find($id);    

        $equipments =  DB::table('equipments')
                ->select('*')
                ->where('is_delete',0)
                ->get();

        return view('maintenance.edit',[
            'maintenance' => $maintenance,
            'equipments' =>$equipments,

        ]);
        
    }
    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::find($id);    

        $validatedData = $request->validate([
            'equipment' => ['required'],
            'details' => ['required'],
            ],
            [
                'equipment.required' => '  ادخل       اسم   المُـــعده    ',
                'details.required' => '  ادخل    الأعمال التي يجب تنفيذها   ',
            ]);
    
            $body = '';
            if($maintenance->equipment != $request->equipment) {
                $equipment1 = DB::table('equipments')->select('*')->where('id' ,$maintenance->equipment)->first();
                $equipment2 = DB::table('equipments')->select('*')->where('id' ,$request->equipment)->first();
                $body .=  '  تم تغيير اسم المُـــعده  من ' . $equipment1->name. ' الى '.$equipment2->name;
                $body.= "\r\n /";
            } 
            if($maintenance->details != $request->details){
                    $body .=  '  تم تغيير الأعمال التي يجب تنفيذها  من '.$maintenance->details. ' الى '.$request->details;
                    $body.= "\r\n /";
            }   
            
            $maintenance->equipment = $request->equipment;
            $maintenance->details = $request->details;
            $maintenance->notes = $request->notes;
            $maintenance->save();

            $title = 'تم   تعديل طلب إصلاح مُعِــــده  ';

            $request->session()->flash('EditMaintenance', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/maintenance', 'action');

            event(new Notifications($title));
            return back();

    }
    public function destroy($id)
    {
            $maintenance = Maintenance::find($id);    
            $maintenance->is_delete  = 1;
            $maintenance->save();

            $title = 'تم حذف طلب إصلاح  مُعِــــده بنجاح';

            $auth = new AuthController();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$maintenance->equipment)->first();

            $body =  '  تم حذف طلب إصلاح  مُعِــــده جديد    ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipment->name;$body.= "\r\n /";
            $body .=  'الأعمال التي يجب تنفيذها'.$maintenance->details;$body.= "\r\n /";


            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/maintenance', 'action');

            event(new Notifications($title));
    }

    public function maintenanceData()
    {
            $maintenances = Maintenance::where([['is_delete',0]])->orderby('created_at','desc')->get();
            

            foreach ($maintenances as  $maintenance) {
                    $equipment = DB::table('equipments')->select('*')->where('id' ,$maintenance->equipment)->first();

                    $maintenance->equipment   = $equipment->name;
                  
                }

                    
            return DataTables::of($maintenances) 
                    ->addColumn('action', function($maintenance) {
                        $action = '';
                    $auth = new AuthController();
                     if($maintenance->status ==0){
                    if($auth->canView('maintenance', 'write')){
                        $action .='<a  href="/maintenance/' . $maintenance->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    
                    if($auth->canView('maintenance', 'delete')){
                        $action .='<a  coords="' . $maintenance->equipment . '"  id="' . $maintenance->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                }else{
                    $action .='<a  href="/maintenance/print/' . $maintenance->id . '" class="edit-button"><i class="fas fa-print"></i> </a>';
                }
                    return $action;
                })
                ->addColumn('action2', function($maintenance) {
                        $action2 = '';
                       
                    $auth = new AuthController();
                    if($auth->canView('maintenance', 'write')){
                         if($maintenance->status ==0){
                        $action2 .='<a  href="/maintenance/' . $maintenance->id . '" class="edit-button"><i class="fas fa-sign-out"></i> </a>';
                         }else{
                             $action2.='تم إنهاء الطلب';
                         }
                    }
                    return $action2;                })
                ->escapeColumns(['action2' => 'action2'])
                ->rawColumns(['action2'])
                ->make(true);
            
    }
    public function done(Request $request, $id,  TelegramNotifications  $telegram)
    {
        $maintenance = Maintenance::find($id);    

        $validatedData = $request->validate([
            'after_details' => ['required'],
            'employees' => ['required'],
            ],
            [
                'after_details.required' => '  ادخل  الأعمال التي تم تنفيذها     ', 
                'employees.required' => '  ادخل  اسم القائم بالعمل        ', 
            ]);

    
            $maintenance->status = 1;
            $maintenance->after_details =$request->after_details;
            $maintenance->employees = join("-",$request->employees);
            $maintenance->after_notes = $request->after_notes;
            $maintenance->save();

            $equipment = DB::table('equipments')->select('*')->where('id' ,$maintenance->equipment)->first();

             $employees = '';
            foreach ($request->employees as $employee) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$employee)->first();
                $employee .= $emp->name .' - ';
            }

            $title = ' تم  إنهاء طلب إصلاح     ';
            $body =  ' تم  إنهاء طلب إصلاح    ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipment->name;$body.= "\r\n /";
            $body .=  '      الأعمال التي يجب تنفيذها    '.$maintenance->details;$body.= "\r\n /";
            $body .=  '      الأعمال التي تم تنفيذها     '.$request->after_details;$body.= "\r\n /";
            $body .=  'اسم القائم بالعمل  '.$employees;$body.= "\r\n";

            $message =  'تم إنهاء طلب إصلاح '.$equipment->name;$message.= "\r\n";
            $message .=  'الأعمال التي تم تنفيذها '.$request->after_details;$message.= "\r\n";
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/maintenance', 'action');

            event(new Notifications($title));
            $telegram->send($message, 'data');
            return redirect('maintenance')->with('done',$title);
            
    }
        public function print($id){
        $maintenance = Maintenance::find($id);    

        $equipment = DB::table('equipments')->select('*')->where('id' ,$maintenance->equipment)->first();
    
        
        $emps = explode("-",$maintenance->employees );
        $employees = '';
            foreach ($emps as $employee) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$employee)->first();
                $employees .= $emp->name .' - ';
            }

        $maintenance->equipment = $equipment->name;
        $maintenance->employees = rtrim($employees, ' - ');

        return view('maintenance.print',[
            'maintenance' => $maintenance
        ]);
    } 
}
