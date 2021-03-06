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
                'equipment.required' => '  ????????       ??????   ????????????????????    ',
                'duration.required' => '  ????????  ?????? ?????????????? ????????????????    ',
                'company.required' => '  ????????    ???????????? ?????????????? ??????   ',
                'vessel.required' => '  ????????   ?????? ??????????????      ',
                'plan.required' => '  ????????    ?????? ?????? ?????????????? / ??????????    ',
                'driver.required' => '  ????????    ?????? ????????????   ',
                'details.required' => '  ????????      ?????????????? ?????????? ?????? ????????????   ',
                
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

            $title = ' ( ?????????? ?????????? )????   ?????????? ?????? ???????? ???????????????????? ????????  ';
            $body =  '  ???? ?????????? ?????? ???????? ???????????????????? ????????    ';$body.= "\r\n /";
            $body .=  '?????? ????????????   '.$equipments;$body.= "\r\n /";
            $body .=  '?????? ??????????????   '.$request->vessel;$body.= "\r\n /";
            $body .=  '    ?????? ?????????????? ????????????????  '.$request->duration;$body.= "\r\n /";
            $body .=  ' ???????????? ?????????????? ??????  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '      ?????? ?????? ?????????????? / ??????????  '.$request->plan;$body.= "\r\n /";
            $body .=  '   ?????? ????????????  '.$drivers;$body.= "\r\n /";
            $body .=  '      ?????????????? ?????????? ?????? ????????????  '.$request->details;$body.= "\r\n /";

            $message =  ' ???? ??????  ?????????? ???????? ?? '.$equipments;$message.= "\r\n";
            $message .=  '?????? ??????????????   '.$request->vessel;$message.= "\r\n";
            $message .=  '?????? ?????????????? ???????????????? '.$request->duration.' ????????' ;$message.= "\r\n";            
            $message .=  '?????? ???????????? '.$drivers;$message.= "\r\n";
            $message .=  '???????????? ?????????????? ?????? '.$company->name_ar;$message.= "\r\n";
            $message .=  '?????? ?????? ?????????????? / ?????????? '.$request->plan;$message.= "\r\n";
            $message .=  '?????????????? ?????????? ?????? ???????????? '.$request->details;$message.= "\r\n";

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
                    $vessel->duration   = $vessel->duration. ' ???????? ';
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
                    $action2 .='???? ???????? ????????????';
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
                'after_details.required' => '  ????????  ?????????????? ?????????? ??????  ????????????????    ', 
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

            $title = ' (?????????? ??????????)  ????  ????????  ????????????????   ';
            $body =  ' (?????????? ??????????)  ????  ????????  ????????????????    ';$body.= "\r\n /";
            $body .=  '?????? ????????????   '.$equipments;$body.= "\r\n /";
            $body .=  '?????? ??????????????   '.$vessels->vessel;$body.= "\r\n /";
            $body .=  '    ?????? ?????????????? ????????????????  '.$vessels->duration;$body.= "\r\n /";
            $body .=  ' ???????????? ?????????????? ??????  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '      ?????? ?????? ?????????????? / ??????????  '.$vessels->plan;$body.= "\r\n /";
            $body .=  '   ?????? ????????????  '.$vessels->driver;$body.= "\r\n /";
            $body .=  '      ?????????????? ?????????? ?????? ????????????  '.$vessels->details;$body.= "\r\n /";
            $body .=  '      ?????????????? ?????????? ?????? ????????????????    '.$request->after_details;$body.= "\r\n /";

            $message =  '???? ???????? '.$equipments;$message.= "\r\n";
            $message .=  '?????? ?????????????? '.$vessels->vessel;$message.= "\r\n";
            $message .=  '?????????????? ?????????? ?????? ???????????????? '.$request->after_details;$message.= "\r\n";

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
