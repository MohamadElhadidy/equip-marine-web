<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;
use App\Notifications\TelegramNotifications;

class EquipmentController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:equipments,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            ]
        ]);
        $this->middleware("canView:equipments,read", [
        'only' => [
            'index' ,
            'equipmentsData'
            ]
        ]);
        $this->middleware("canView:equipments,delete", [
        'only' => [
            'destroy' ,
            'restore',
            'trash' ,
            'equipmentsTrash'
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
         $auth = new AuthController();
        $title = 'تم   الدخول على تقرير المعدات';
        $body = $title;
        $auth->notify(auth()->user()->id, 1, $title, $body, '/users', 'move');
        event(new Notifications($title));
        return view('equipments.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups =  DB::table('groups')
                ->select('*')
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();

        $auth = new AuthController();
        $title = 'تم   الدخول على إضافة معده';
        $body = $title;
        $auth->notify(auth()->user()->id, 1, $title, $body, '/users', 'move');
        event(new Notifications($title));
        return view('equipments.create',[
            'groups' =>$groups,
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
    public function store(Request $request,  TelegramNotifications  $telegram)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:equipments'],
            'name' => ['required','max:255', 'unique:equipments'],
            'power' => ['required'],
            'group' => ['required'],
            'company' => ['required'],
            'location' => ['required'],
            'ownership' => ['required'],
            'ownership_date' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المُـــعده   موجود  ',
                'code.required' => '  ادخل    كـــود المُـــعده   ',

                'name.unique' => ' إســـــم المُـــعده    موجود  ',
                'name.required' => ' ادخل إســـــم المُـــعده ',

                'power.required' => '  ادخل       السعة / القدرة    ',
                'group.required' => '  ادخل  المجموعة    ',
                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'ownership.required' => '  ادخل    الملكية   ',
                'ownership_date.required' => '  ادخل    تاريخ التعاقد   ',
                
            ]);
            $equipment = new Equipment;
    
            $equipment->code =$request->code;
            $equipment->name = $request->name;
            $equipment->power = $request->power;
            $equipment->groups = $request->group;
            $equipment->company = $request->company;
            $equipment->location = $request->location;
            $equipment->ownership = $request->ownership;
            $equipment->ownership_date = $request->ownership_date;
            $equipment->notes = $request->notes;
            $equipment->save();

            $group = DB::table('groups')->select('*')->where('id' ,$request->group)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$request->location)->first();

            $title = 'تم   إضافة مُعِــــده جديدة ';
            $body =  ' تم إضافة '.$request->name;$body.= "\r\n /";
            $body .=  ' كود '.$request->code;$body.= "\r\n /";
            $body .=  '  السعة / القدرة '.$request->power;$body.= "\r\n /";
            $body .=  ' المجموعة '.$group->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها '.$company->name_ar;$body.= "\r\n /";
            $body .=  ' الموقع '.$location->name;$body.= "\r\n /";
            $body .=  ' الملكية '.$request->ownership;$body.= "\r\n /";

            $message =  ' تم إضافة '.$request->name;$message.= "\r\n ";
            $message .=  'كود '.$request->code;$message.= "\r\n ";
            $message .=  'السعة / القدرة '.$request->power;$message.= "\r\n ";
            $message .=  'المجموعة '.$group->name;$message.= "\r\n ";
            $message .=  'الشركة التابعة لها '.$company->name_ar;$message.= "\r\n ";
            $message .=  'الموقع '.$location->name;$message.= "\r\n ";
            $message .=  'الملكية '.$request->ownership;$message.= "\r\n ";



            $request->session()->flash('NewEquipment', $title);
            $telegram->send($message,'data');
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/equipments', 'action');

            event(new Notifications($title));

            return  back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Equipment $equipment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipment $equipment)
    {

        $groups =  DB::table('groups')
                ->select('*')
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();
        
        return view('equipments.edit',[
            'equipment' => $equipment,
            'groups' =>$groups,
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
    public function update(Request $request, Equipment $equipment,  TelegramNotifications  $telegram)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:equipments,code,'.$equipment->id],
            'name' => ['required','max:255',  'unique:equipments,name,'.$equipment->id],
            'power' => ['required'],
            'group' => ['required'],
            'company' => ['required'],
            'location' => ['required'],
            'ownership' => ['required'],
            'ownership_date' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المُـــعده   موجود  ',
                'code.required' => '  ادخل    كـــود المُـــعده   ',

                'name.unique' => ' إســـــم المُـــعده    موجود  ',
                'name.required' => ' ادخل إســـــم المُـــعده ',

                'power.required' => '  ادخل       السعة / القدرة    ',
                'group.required' => '  ادخل  المجموعة    ',
                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'ownership.required' => '  ادخل    الملكية   ',
                'ownership_date.required' => '  ادخل    تاريخ التعاقد   ',
                
            ]);
            $body = '';
            $message = '';
            if($equipment->code != $request->code){
                    $body .=  '  تم تغيير كود المُـــعده من '.$equipment->code.  ' *الى* '.$request->code;
                    $body.= "\r\n /";
                    $message .=  'تم تغيير كود المُـــعده من '.$equipment->code.  ' *الى* '.$request->code;
                    $message.= "\r\n ";
                    
            }   
            if($equipment->name != $request->name) {
                $body .=  '  تم تغيير اسم المُـــعده من ' . $equipment->name.  ' *الى* '.$request->name;
                $body.= "\r\n /";
                $message .=  'تم تغيير اسم المُـــعده من ' . $equipment->name.  ' *الى* '.$request->name;
                $message.= "\r\n";
            }
            if($equipment->power != $request->power) {
                $body .=  '  تم تغيير  السعة / القدرة   من ' . $equipment->power.  ' *الى* '.$request->power;
                $body.= "\r\n /";
                $message .=  'تم تغيير السعة / القدرة من ' . $equipment->power.  ' *الى* '.$request->power;
                $message.= "\r\n ";
            }
            if($equipment->groups != $request->group) {
                $group1 = DB::table('groups')->select('*')->where('id' ,$equipment->groups)->first();
                $group2 = DB::table('groups')->select('*')->where('id' ,$request->group)->first();
                $body .=  '  تم تغيير المجموعة  من ' . $group1->name.  ' *الى* '.$group2->name;
                $body.= "\r\n /";
                $message .=  'تم تغيير المجموعة من ' . $group1->name.  ' *الى* '.$group2->name;
                $message.= "\r\n";
            } 
            if($equipment->company != $request->company) {
                $company1 = DB::table('hr.companies')->select('*')->where('id' ,$equipment->company)->first();
                $company2 = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                $body .=  '  تم تغيير   الشركة التابعة لها  من ' . $company1->name_ar.  ' *الى* '.$company2->name_ar;
                $body.= "\r\n /";
                $message .=  'تم تغيير الشركة التابعة لها من ' . $company1->name_ar.  ' *الى* '.$company2->name_ar;
                $message.= "\r\n";
            }
            if($equipment->location != $request->location) {
                $location1 = DB::table('locations')->select('*')->where('id' ,$equipment->location)->first();
                $location2 = DB::table('locations')->select('*')->where('id' ,$request->location)->first();
                $body .=  '  تم تغيير الموقع  من ' . $location1->name.  ' *الى* '.$location2->name;
                $body.= "\r\n /";
                $message .=  'تم تغيير الموقع من ' . $location1->name.  ' *الى* '.$location2->name;
                $message.= "\r\n";
            }
            if($equipment->ownership != $request->ownership) {
                $body .=  '  تم تغيير  الملكية من ' . $equipment->ownership.  ' *الى* '.$request->ownership;
                $body.= "\r\n /";
                $message .=  'تم تغيير الملكية من ' . $equipment->ownership. ' *الى* '.$request->ownership;
                $message.= "\r\n";
            }
            if($equipment->ownership_date != $request->ownership_date) {
                $body .=  '  تم تغيير تاريخ التعاقد من ' . $equipment->ownership_date.  ' *الى* '.$request->ownership_date;
                $body.= "\r\n /";
            }
 
            $equipment->code =$request->code;
            $equipment->name = $request->name;
            $equipment->power = $request->power;
            $equipment->groups = $request->group;
            $equipment->company = $request->company;
            $equipment->location = $request->location;
            $equipment->ownership = $request->ownership;
            $equipment->ownership_date = $request->ownership_date;
            $equipment->notes = $request->notes;
            $equipment->save();


            $title = 'تم تعديل المُـــعده بنجاح';


            $request->session()->flash('EditEquipment', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/equipments', 'action');

        event(new Notifications($title));


        $telegram->send($message,'data');
        $groups =  DB::table('groups')
                ->select('*')
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();
        
        return  back()->with([
            'equipment' => $equipment,
            'groups' =>$groups,
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
    public function destroy($id,  TelegramNotifications  $telegram)
    {
            $equipment = Equipment::find($id);    
            $equipment->is_delete  = 1;
            $equipment->save();

            $title = 'تم حذف المُـــعده بنجاح';

            $auth = new AuthController();

            $group = DB::table('groups')->select('*')->where('id' ,$equipment->groups)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$equipment->company)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$equipment->location)->first();

            $title = 'تم   حذف مُعِــــده  ';
            $body =  '  تم حذف مُعِــــده   كود  '.$equipment->code;$body.= "\r\n /";
            $body .=  'اسم   '.$equipment->name;$body.= "\r\n /";
            $body .=  '  السعة / القدرة  '.$equipment->power;$body.= "\r\n /";
            $body .=  ' المجموعة  '.$group->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";
            $body .=  '   الملكية  '.$equipment->ownership;$body.= "\r\n /";

            $message =  ' تم حذف '.$equipment->name;$message.= "\r\n ";
            $message .=  'كود '.$equipment->code;$message.= "\r\n ";
            $message .=  'السعة / القدرة '.$equipment->power;$message.= "\r\n ";
            $message .=  'المجموعة '.$group->name;$message.= "\r\n ";
            $message .=  'الشركة التابعة لها '.$company->name_ar;$message.= "\r\n ";
            $message .=  'الموقع '.$location->name;$message.= "\r\n ";
            $message .=  'الملكية '.$equipment->ownership;$message.= "\r\n ";



            $telegram->send($message,'data');
            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/equipments', 'action');

            event(new Notifications($title));

    }
   
    public function equipmentsData()
    {
            $equipments = Equipment::where('is_delete',0)->get();
            

            foreach ($equipments as  $equipment) {
                    $groups = DB::table('groups')->select('*')->where('id' ,$equipment->groups)->first();
                    $conditions = DB::table('conditions')->select('*')->where('id' ,$equipment->conditions)->first();
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$equipment->company)->first();
                    $location = DB::table('locations')->select('*')->where('id' ,$equipment->location)->first();

                    $equipment->groups   = $groups->name;
                    $equipment->conditions   = $conditions->name;
                    $equipment->company   = $company->name_ar;
                    $equipment->location   = $location->name;
                }
                
            return DataTables::of($equipments) 
                ->addColumn('action', function ($equipment) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('equipments', 'write')){
                        $action .='<a  href="/equipments/' . $equipment->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canView('equipments', 'delete')){
                        $action .='<a  coords="' . $equipment->name . '"  id="' . $equipment->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function trash()
    {
         $auth = new AuthController();
        $title = 'تم   الدخول على تقرير المحذوف للمعدات';
        $body = $title;
        $auth->notify(auth()->user()->id, 1, $title, $body, '/users', 'move');
        event(new Notifications($title));
        return view('equipments.trash');
    }
    
        public function equipmentsTrash()
    {
            $equipments = Equipment::where('is_delete',1)->get();
            

            foreach ($equipments as  $equipment) {
                    $groups = DB::table('groups')->select('*')->where('id' ,$equipment->groups)->first();
                    $conditions = DB::table('conditions')->select('*')->where('id' ,$equipment->conditions)->first();
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$equipment->company)->first();
                    $location = DB::table('locations')->select('*')->where('id' ,$equipment->location)->first();

                    $equipment->groups   = $groups->name;
                    $equipment->conditions   = $conditions->name;
                    $equipment->company   = $company->name_ar;
                    $equipment->location   = $location->name;
                }
                
            return DataTables::of($equipments) 
                ->addColumn('action', function ($equipment) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('equipments', 'delete')){
                        $action .='<a  coords="' . $equipment->name . '"  id="' . $equipment->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function restore($id)
    {
            $equipment = Equipment::find($id);    
            $equipment->is_delete  = 0;
            $equipment->save();

            $title = 'تم استرجاع المُـــعده بنجاح';

            $auth = new AuthController();

            $group = DB::table('groups')->select('*')->where('id' ,$equipment->groups)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$equipment->company)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$equipment->location)->first();

            $title = 'تم   استرجاع مُعِــــده  ';
            $body =  '  تم استرجاع مُعِــــده   كود  '.$equipment->code;$body.= "\r\n /";
            $body .=  'اسم   '.$equipment->name;$body.= "\r\n /";
            $body .=  '  السعة / القدرة  '.$equipment->power;$body.= "\r\n /";
            $body .=  ' المجموعة  '.$group->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";
            $body .=  '   الملكية  '.$equipment->ownership;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/equipments', 'action');

            event(new Notifications($title));

    }


}
