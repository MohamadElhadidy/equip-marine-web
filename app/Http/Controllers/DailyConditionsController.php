<?php

namespace App\Http\Controllers;

use App\Models\DailyConditions;
use App\Models\DailyConditionsIds;
use App\Models\Equipment;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;
use App\Notifications\TelegramNotifications;

class DailyConditionsController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:dailyConditions,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            'save'
            ]
        ]);
        $this->middleware("canView:dailyConditions,read", [
        'only' => [
            'index' ,
            'dailyConditionsData'
            ]
        ]);
        $this->middleware("canView:DailyConditions,delete", [
        'only' => [
            'destroy' ,
            'restore',
            'trash' ,
            'dailyConditionsTrash'
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
        return view('dailyConditions.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dailyConditions.create');
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
            'date' => ['required', 'max:255' , 'unique:dailyConditionsIds'],
            ],
            [
                'date.unique' => '    التاريخ   موجود  من قبل ',
                'date.required' => '  ادخل التاريخ',
            ]);
            $dailyCondition = new DailyConditionsIds;
    
        $dailyCondition->date = $request->date;
        $dailyCondition->notes = $request->notes;
        $dailyCondition->save();
        $title = 'تم   إضافة  تقرير يومي  جديد ';
        $body =  '  تم إضافة تقرير يومي   جديد  ';$body.= "\r\n /";
        $body .=  '    التاريخ     '.$request->date;$body.= "\r\n /";
        $message = 'تم إضافة الموقف اليومي للمعدات بتاريخ '.$request->date;
        $request->session()->flash('NewDaily', $title);
        
        $auth = new AuthController();
        $auth->notify(auth()->user()->id, 2, $title, $body, '/dailyConditions', 'action');
        event(new Notifications($title));
        $telegram->send($message,'data');
        return  back();
    }


    public function show($id)
    {
        $conditions =  DB::table('conditions')
                ->select('*')
                ->get();
        $equipments =  DB::table('dailyConditions')
                ->select('*')
                ->where('is_delete','0')
                ->where('daily_conditions_id',$id)
                ->get();
        $employees =  DB::table('hr.data')
                ->select('*')
                ->get();
        if($equipments->isEmpty())  {
            $equipments =  DB::table('equipments')
                ->select('*')
                ->where('is_delete','0')
                ->get();
                foreach ($equipments as  $equipment) {
                        $equipment->ids   = '';
            }
        }else{

        foreach ($equipments as  $equipment) {
            $equip = DB::table('equipments')->select('*')->where('id' ,$equipment->name)->first();
            $equipment->ids   = $equipment->id;
            $equipment->id   = $equip->id;
            $equipment->name   = $equip->name;
            $equipment->code   = $equip->code;
            $equipment->employee   = explode("-",$equipment->employee);
            }
        }
        if($equipments->isEmpty()) return redirect('/dailyConditions');
        return view('dailyConditions.show',[
            'equipments' => $equipments,
            'conditions' => $conditions,
            'employees' => $employees,
            'id' => $id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit( $id)
    {  
        $dailyConditionsId = DailyConditionsIds::find($id);    
        return view('dailyConditions.edit',[
            'dailyConditionsId' => $dailyConditionsId
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dailyConditionsId = DailyConditionsIds::find($id);    
        $validatedData = $request->validate([
            'date' => ['required', 'max:255' , 'unique:dailyConditionsIds,date,'.$dailyConditionsId->id],
            ],
            [
                'date.unique' => '    التاريخ   موجود  من قبل ',
                'date.required' => '  ادخل     التاريخ       ',
            ]);
            $body = '';
            if($dailyConditionsId->date != $request->date){
                    $body .=  '  تم تغيير  التاريخ من '.$dailyConditionsId->date. ' الى '.$request->date;
                    $body.= "\r\n /";
            }  

            $dailyConditionsId->date = $request->date;
            $dailyConditionsId->notes = $request->notes;
            $dailyConditionsId->save();


            $title = 'تم تعديل تقرير يومي بنجاح';


            $request->session()->flash('EditDaily', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/dailyConditions', 'action');

        event(new Notifications($title));

        return  back();
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
            $dailyConditionsId = DailyConditionsIds::find($id);    
            $dailyConditionsId->is_delete  = 1;
            $dailyConditionsId->save();

            $title = 'تم حذف تقرير يومي بنجاح';

            $auth = new AuthController();


            $body =  '  تم حذف  تقرير يومي   تاريخ  '.$dailyConditionsId->date;$body.= "\r\n /";


            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/dailyConditions', 'action');

            event(new Notifications($title));

    }
   
    public function dailyConditionsData()
    {
            $dailyConditionsIds = DailyConditionsIds::where('is_delete',0)->get();
                
            return DataTables::of($dailyConditionsIds) 
                ->addColumn('action', function ($dailyConditionsId) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('dailyConditions', 'write')){
                        $action .='<a  href="/dailyConditions/' . $dailyConditionsId->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canView('dailyConditions', 'delete')){
                        $action .='<a  coords="' . $dailyConditionsId->date . '"  id="' . $dailyConditionsId->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })
                ->addColumn('action2', function($dailyConditionsId) {
                        $action2 = '';
                       
                    $auth = new AuthController();
                    if($auth->canView('dailyConditions', 'write')){
                        $action2 .='<a  href="/dailyConditions/print/' . $dailyConditionsId->id .'" class="edit-button"><i class="fas fa-print"></i></a>';
                       
                    }
                    return $action2;})
                    ->addColumn('action3', function($dailyConditionsId) {
                        $action3 = '';
                    $auth = new AuthController();
                    if($auth->canView('dailyConditions', 'write')){
                        $action3 .='<a  href="/dailyConditions/' . $dailyConditionsId->id .'" class="edit-button"><i class="fas fa-plus-square"></i></a>';
                    
                    }
                    return $action3; } )
                ->escapeColumns(['action2' => 'action2'])
                ->escapeColumns(['action3' => 'action3'])
                ->rawColumns(['action2'])
                ->rawColumns(['action3'])
                ->make(true);
            
    }
    public function save(Request $request)
    {
            $dailyConditionsId = DailyConditionsIds::find($request->id); 
            $dailyConditions = DailyConditions::where('daily_conditions_id',$request->id)->first();
            $dailyConditionsIds = DailyConditionsIds::orderby('date','desc')->first();
            $ignore = 0;
            if($dailyConditionsId->date <  $dailyConditionsIds->date) $ignore = 1;
            if($dailyConditions == null ){
                for ($i=0; $i <  count($request->name) ; $i++) { 
                $dailyCondition = new DailyConditions;
                
                $dailyCondition->daily_conditions_id = $request->id;
                $dailyCondition->name = $request->name[$i];
                $dailyCondition->conditions = $request->conditions[$i];
                if (isset($request->employee[$request->name[$i]])) {
                        $dailyCondition->employee = join("-",$request->employee[$request->name[$i]]); 
                }
                    
                
                $dailyCondition->notes = $request->notes[$i];
                $dailyCondition->save();
                if ( $ignore  ==0) {
                    $equipment = Equipment::find($request->name[$i]);    
                    $equipment->conditions = $request->conditions[$i];
                    $equipment->save();
                }
            
            }
            }else {
                for ($i=0; $i <  count($request->name) ; $i++) { 
                    
                $dailyCondition = DailyConditions::find($request->ids[$i]); 

                $dailyCondition->name = $request->name[$i];
                $dailyCondition->conditions = $request->conditions[$i];
                if (isset($request->employee[$request->name[$i]])) {
                        $dailyCondition->employee = join("-",$request->employee[$request->name[$i]]); 
                }
                $dailyCondition->notes = $request->notes[$i];
                $dailyCondition->save();

                if ( $ignore  ==0) {
                    $equipment = Equipment::find($request->name[$i]);    
                    $equipment->conditions = $request->conditions[$i];
                    $equipment->save();
                }
            }
            }

            $title = 'تم   تحديث الحالة الفنية       ';
            $body =  '  تم   تحديث الحالة الفنية  ';$body.= "\r\n /";
            $body .=  '    التاريخ     '.$dailyConditionsId->date;$body.= "\r\n /";
            $request->session()->flash('NewUpdate', $title);
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/dailyConditions', 'action');

            event(new Notifications($title));

            return  back();
    }


    public function trash()
    {
        return view('dailyConditions.trash');
    }
    
        public function dailyConditionsTrash()
    {
           $dailyConditionsIds = DailyConditionsIds::where('is_delete',1)->get();
                
            return DataTables::of($dailyConditionsIds) 
                ->addColumn('action', function ($dailyConditionsId) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('dailyConditions', 'delete')){
                        $action .='<a  coords="' . $dailyConditionsId->date . '"  id="' . $dailyConditionsId->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function restore($id)
    {

            $dailyConditionsId = DailyConditionsIds::find($id);    
            $dailyConditionsId->is_delete  = 0;
            $dailyConditionsId->save();

            $title = 'تم استرجاع تقرير يومي بنجاح';

            $auth = new AuthController();


            $body =  '  تم استرجاع  تقرير يومي   تاريخ  '.$dailyConditionsId->date;$body.= "\r\n /";


            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/dailyConditions', 'action');

            event(new Notifications($title));

    }
    public function print($id)
    {
        
        $conditions =  DB::table('conditions')
                ->select('*')
                ->get();

        $groups =  DB::table('groups')
                ->select('*')
                ->get();
        $employees =  DB::table('hr.data')
                ->select('*')
                ->get();
        $equipments =  DB::table('dailyConditions')
                ->select('*')
                ->where('is_delete','0')
                ->where('daily_conditions_id',$id)
                ->get();
        if($equipments->isEmpty())      return redirect('/dailyConditions');



        foreach ($equipments as  $equipment) {
            $equip = DB::table('equipments')->select('*')->where('id' ,$equipment->name)->first();
            $equipment->ids   = $equipment->id;
            $equipment->id   = $equip->id;
            $equipment->name   = $equip->name;
            $equipment->code   = $equip->code;
            $equipment->group   = $equip->groups;

            $emps = explode("-",$equipment->employee);
            $equipment->employee = '';
            foreach ($emps as $emp) {
                $em =DB::table('hr.data')->select('*')->where('id' ,$emp)->first();
                if(isset($em->name)) $equipment->employee .= $em->name .' - ';
                
            }
            $equipment->employee =  rtrim($equipment->employee, ' - ');
            }
        
        $dailyConditionsId = DailyConditionsIds::find($id);    
        $date = date("d-m-Y", strtotime($dailyConditionsId->date));
        return view('dailyConditions.print',[
            'equipments' => $equipments,
            'conditions' => $conditions,
            'groups' => $groups,
            'date'=>$date
        ]);
    }

}
