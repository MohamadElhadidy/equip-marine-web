<?php

namespace App\Http\Controllers;

use App\Models\Vessel;
use App\Models\Requests;
use App\Models\requestsOut;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Events\Notifications;
use DataTables;
use DB;
use App\Notifications\TelegramNotifications;

class VesselsController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:vessels,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            'done'
            ]
        ]);
        $this->middleware("canView:vessels,read", [
        'only' => [
            'index' ,
            'vesselsData'
            ]
        ]);
        $this->middleware("canView:vessels,delete", [
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
        return view('vessels.report');
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
        $drivers =  DB::table('hr.data')
                ->select('*')
                ->wherein('job',[8,9,10,12,13,57,66,67,68,69,70,71,115,124,125,126,127,128,129,173])
                ->get();
        $unloading = DB::table('unloading.vessels_log')->select('id','name')->where('done',0);
        $shipping = DB::table('shipping.vessels_log')->select('id','name')->where('done',0);

        $vessels = $unloading->unionAll($shipping)->get();
    
        return view('vessels.create',[
            'equipments' =>$equipments,
            'companies' =>$companies,
            'drivers' =>$drivers,
            'vessels' =>$vessels
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
            'vessel' => ['required'],
            'plan' => ['required'],
            'company' => ['required'],
            'driver' => ['required'],
            'details' => ['required'],
            ],
            [
                'equipment.required' => '  ادخل       اسم   المُـــعده    ',
                'duration.required' => '  ادخل  مدة التشغيل المتوقعة    ',
                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'vessel.required' => '  ادخل   اسم الباخرة      ',
                'plan.required' => '  ادخل    رقم خطة التفريغ / الشحن    ',
                'driver.required' => '  ادخل    اسم السائق   ',
                'details.required' => '  ادخل      ملاحظات الفحص قبل التحرك   ',
                
            ]);
            $vessel = new Vessel;
    
            $vessel->equipment = join("-",$request->equipment);
            $vessel->duration = $request->duration;
            $vessel->company = $request->company;
            $vessel->vessel = $request->vessel;
            $vessel->plan = $request->plan;
            $vessel->driver =  join("-",$request->driver);
            $vessel->details = $request->details;
            $vessel->notes = $request->notes;
            $vessel->save();

            $equipments = '';
            foreach ($request->equipment as $equipment) {
                $equip = Equipment::find($equipment);
                $equipments .= $equip->name .' - ';
                $equip->conditions = 2 ;
                $equip->save();
            }
            $drivers = '';
            foreach ($request->driver as $driver) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$driver)->first();
                $drivers .= $emp->name .' - ';
            }
            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();

            $title = ' ( تشغيل السفن )تم   إضافة إذن خروج مُعِــــده جديد  ';
            $body =  '  تم إضافة إذن خروج مُعِــــده جديد    ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipments;$body.= "\r\n /";
            $body .=  'اسم الباخرة   '.$request->vessel;$body.= "\r\n /";
            $body .=  '    مدة التشغيل المتوقعة  '.$request->duration;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '      رقم خطة التفريغ / الشحن  '.$request->plan;$body.= "\r\n /";
            $body .=  '   اسم السائق  '.$drivers;$body.= "\r\n /";
            $body .=  '      ملاحظات الفحص قبل التحرك  '.$request->details;$body.= "\r\n /";

            $message =  ' تم عمل  تصريح خروج ل '.$equipments;$message.= "\r\n";
            $message .=  'اسم الباخرة   '.$request->vessel;$message.= "\r\n";
            $message .=  'مدة التشغيل المتوقعة '.$request->duration.' ساعه' ;$message.= "\r\n";            
            $message .=  'اسم السائق '.$drivers;$message.= "\r\n";
            $message .=  'الشركة التابعة لها '.$company->name_ar;$message.= "\r\n";
            $message .=  'رقم خطة التفريغ / الشحن '.$request->plan;$message.= "\r\n";
            $message .=  'ملاحظات الفحص قبل التحرك '.$request->details;$message.= "\r\n";

            $request->session()->flash('NewVessel', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/vessels', 'action');

            event(new Notifications($title));
            $telegram->send($message,'operation');
            return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vessel = Vessel::find($id);
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();


        $unloading = DB::table('unloading.vessels_log')->select('id','name') ;
        $shipping = DB::table('shipping.vessels_log')->select('id','name') ;

        $vessels = $unloading->unionAll($shipping)->get();
    
        $equips = explode("-",$vessel->equipment);
        $equipments = '';
            foreach ($equips as $equipment) {
                $equip = Equipment::find($equipment);
                $equipments .= $equip->name .' - ';
            }
        $emps = explode("-",$vessel->driver);
        $drivers = '';
            foreach ($emps as $driver) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$driver)->first();
                $drivers .= $emp->name .' - ';
            }
            
        return view('vessels.show',[
            'vessel' =>$vessel,
            'vessels' =>$vessels,
            'drivers' =>$drivers,
            'equipments' =>$equipments,
            'companies' =>$companies,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vessel = Vessel::find($id);    
        $equipments =  DB::table('equipments')
                ->select('*')
                ->where('is_delete',0)
                ->where('conditions', '!=' , 2)
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();

        $unloading = DB::table('unloading.vessels_log')->select('id','name')->groupby('name');
        $shipping = DB::table('shipping.vessels_log')->select('id','name')->groupby('name');

        $vessels = $unloading->unionAll($shipping)->get();
    
        return view('vessels.edit',[
            'equipments' =>$equipments,
            'vessel' =>$vessel,
            'companies' =>$companies,
            'vessels' =>$vessels
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vessel $vessel)
    {
        //
    }

 
    public function destroy(Vessel $vessel)
    {
        //
    }
        public function vesselsData()
    {
            $vessels = Vessel::where('is_delete',0)->orderby('created_at','desc')->get();
            

            foreach ($vessels as  $vessel) {
                
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$vessel->company)->first();
                    $equips = explode("-",$vessel->equipment);
                    $equipments = '';

                    foreach ($equips as $equipment) {
                        $equip= DB::table('equipments')->select('*')->where('id' ,$equipment)->first();
                        $equipments .= $equip->name . ' - ';
                    }
                    
                    $vessel->company   = $company->name_ar;
                    $vessel->equipment   = $equipments;
                    $vessel->duration   = $vessel->duration. ' ساعة ';
                }
                
            return DataTables::of($vessels) 
                    ->addColumn('action', function($vessel) {
                        $action = '';
                    $auth = new AuthController();
                      if($vessel->status == 1){
                    if($auth->canView('vessels', 'write')){
                       $action .='<a  href="/vessels/print/' . $vessel->id . '" class="edit-button"><i class="fas fa-print"></i> </a>';
                       // $action .='<a  href="/vessels/' . $vessel->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                }
                    // if($auth->canView('vessels', 'delete')){
                    //     $action .='<a  coords="' . $vessel->equipment . '"  id="' . $vessel->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    // }
                    return $action;
                })
                ->addColumn('action2', function($vessel) {
                        $action2 = '';
                    $auth = new AuthController();
                    if($vessel->status == 0){
                    if($auth->canView('vessels', 'write')){
                        $action2 .='<a  href="/vessels/' . $vessel->id . '" class="edit-button"><i class="fas fa-sign-out"></i> </a>';
                    }
                }else{
                    $action2 .='تم رجوع المعده';
                }
                    return $action2;  
                })
                ->escapeColumns(['action2' => 'action2'])
                ->rawColumns(['action2'])
                ->make(true);
    }
       public function done(Request $request, $id,  TelegramNotifications  $telegram)
    {
        $vessels = Vessel::find($id);    

        $validatedData = $request->validate([
            'after_details' => ['required'],
            ],
            [
                'after_details.required' => '  ادخل  ملاحظات الفحص بعد  الإنتهاء    ', 
            ]);

    
            $vessels->status = 1;
            $vessels->after_details =$request->after_details;
            $vessels->after_notes = $request->after_notes;
            $vessels->save();

            $equips = explode("-",$vessels->equipment);
            $equipments = '';
            foreach ($equips as $equipment) {
                $equip = Equipment::find($equipment);
                $equipments .= $equip->name .' - ';
                $equip->conditions = 1 ;
                $equip->save();
            }

        
            
            $company = DB::table('hr.companies')->select('*')->where('id' ,$vessels->company)->first();

            $title = ' (تشغيل السفن)  تم  رجوع  مُـــعده   ';
            $body =  ' (تشغيل السفن)  تم  رجوع  مُـــعده    ';$body.= "\r\n /";
            $body .=  'اسم المعدة   '.$equipments;$body.= "\r\n /";
            $body .=  'اسم الباخرة   '.$vessels->vessel;$body.= "\r\n /";
            $body .=  '    مدة التشغيل المتوقعة  '.$vessels->duration;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '      رقم خطة التفريغ / الشحن  '.$vessels->plan;$body.= "\r\n /";
            $body .=  '   اسم السائق  '.$vessels->driver;$body.= "\r\n /";
            $body .=  '      ملاحظات الفحص قبل التحرك  '.$vessels->details;$body.= "\r\n /";
            $body .=  '      ملاحظات الفحص بعد الإنتهاء    '.$request->after_details;$body.= "\r\n /";

            $message =  'تم رجوع '.$equipments;$message.= "\r\n";
            $message .=  'اسم الباخرة '.$vessels->vessel;$message.= "\r\n";
            $message .=  'ملاحظات الفحص بعد الإنتهاء '.$request->after_details;$message.= "\r\n";

            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/vessels', 'action');

            event(new Notifications($title));
            $telegram->send($message,'operation');
            return redirect('vessels')->with('done',$title);
    }
 public function print($id){
        $vessel= Vessel::find($id);    

        $emps = explode("-",$vessel->driver);
        $drivers = '';
            foreach ($emps as $driver) {
                $emp = DB::table('hr.data')->select('*')->where('id' ,$driver)->first();
                $drivers .= $emp->name .' - ';
            }

        $equips = explode("-",$vessel->equipment);
            $equipments = '';
            foreach ($equips as $equipment) {
                $equip = Equipment::find($equipment);
                $equipments .= $equip->name .' - ';
            }

        $vessel->equipment =  rtrim($equipments, ' - ');
        $vessel->driver = rtrim($drivers, ' - ');

        return view('vessels.print',[
            'vessel' => $vessel,
        ]);
    } 
    
}
