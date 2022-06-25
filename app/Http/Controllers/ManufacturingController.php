<?php

namespace App\Http\Controllers;

use App\Models\Manufacturing;
use App\Models\Equipment;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;


class ManufacturingController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:manufacturing,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'show',
            'update' ,
            'done'
            ]
        ]);
        $this->middleware("canView:manufacturing,read", [
        'only' => [
            'index' ,
            'manufacturingData'
            ]
        ]);
        $this->middleware("canView:manufacturing,delete", [
        'only' => [
            'destroy' ,
            'restore',
            'trash' ,
            'manufacturingTrash'
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
        return view('manufacturing.report');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {    
        return view('manufacturing.create');
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
            'code' => ['required', 'max:255' , 'unique:manufacturing'],
            'name' => ['required','max:255', 'unique:manufacturing'],
            'start_date' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المُـــعده   موجود  ',
                'code.required' => '  ادخل    كـــود المُـــعده   ',

                'name.unique' => ' إســـــم المُـــعده    موجود  ',
                'name.required' => ' ادخل إســـــم المُـــعده ',

                'start_date.required' => '  ادخل     تاريخ بداية التصنيع   ',
                
            ]);
            $manufacturing = new Manufacturing;
    
            $manufacturing->code = str_replace(' ', '',  $request->code);
            $manufacturing->name = $request->name;
            $manufacturing->start_date = $request->start_date;
            $manufacturing->notes = $request->notes;
            $manufacturing->save();


            $title = 'تم   إضافة تصنيع  مُعِــــده جديدة ';
            $body =  '  تم إضافة  تصنيع مُعِــــده جديدة  كود  '.str_replace(' ', '',  $request->code);$body.= "\r\n /";
            $body .=  'اسم   '.$request->name;$body.= "\r\n /";
            $body .=  ' تاريخ بداية التصنيع  '.$request->start_date;$body.= "\r\n /";

            $request->session()->flash('NewManufacturing', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/manufacturing', 'action');

            event(new Notifications($title));

            return  back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Manufacturing $manufacturing)
    {
        if($manufacturing->done == 1)   header( "refresh:3;url=/manufacturing" ); 
           $groups =  DB::table('groups')
                ->select('*')
                ->get();
        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();
        $locations =  DB::table('locations')
                ->select('*')
                ->get();

        return view('manufacturing.done',[
            'manufacturing' => $manufacturing,
            'groups' =>$groups,
            'companies' =>$companies,
            'locations' =>$locations
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Manufacturing $manufacturing)
    {
        return view('manufacturing.edit',[
            'manufacturing' => $manufacturing
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Manufacturing $manufacturing)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:manufacturing,code,'.$manufacturing->id],
            'name' => ['required','max:255',  'unique:manufacturing,name,'.$manufacturing->id],
            'start_date' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المُـــعده   موجود  ',
                'code.required' => '  ادخل    كـــود المُـــعده   ',

                'name.unique' => ' إســـــم المُـــعده    موجود  ',
                'name.required' => ' ادخل إســـــم المُـــعده ',

                'start_date.required' => '  ادخل    تاريخ التعاقد   ',
                
            ]);
            $body = '';
            if($manufacturing->code != $request->code){
                    $body .=  '  تم تغيير كود المُـــعده من '.$manufacturing->code. ' الى '.str_replace(' ', '',  $request->code);
                    $body.= "\r\n /";
            }   
            if($manufacturing->name != $request->name) {
                $body .=  '  تم تغيير اسم المُـــعده من ' . $manufacturing->name. ' الى '.$request->name;
                $body.= "\r\n /";
            }
          
            if($manufacturing->start_date != $request->start_date) {
                $body .=  '  تم تغيير  تاريخ بداية التصنيع  من ' . $manufacturing->start_date. ' الى '.$request->start_date;
                $body.= "\r\n /";
            }
 
            $manufacturing->code =str_replace(' ', '',  $request->code);
            $manufacturing->name = $request->name;
            $manufacturing->start_date = $request->start_date;
            $manufacturing->notes = $request->notes;
            $manufacturing->save();


            $title = 'تم تعديل تصنيع مُـــعده بنجاح';


            $request->session()->flash('EditManufacturing', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/manufacturing', 'action');

        event(new Notifications($title));

        
        return  back()->with([
            'manufacturing' => $manufacturing,
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
            $manufacturing = Manufacturing::find($id);    
            $manufacturing->is_delete  = 1;
            $manufacturing->save();

            $title = 'تم حذف تصنيع مُـــعده بنجاح';

            $auth = new AuthController();


            $title = 'تم   حذف تصنيع  مُعِــــده  ';
            $body =  '  تم حذف تصنيع مُعِــــده   كود  '.$manufacturing->code;$body.= "\r\n /";
            $body .=  'اسم   '.$manufacturing->name;$body.= "\r\n /";
            $body .=  '    تاريخ بداية التصنيع  '.$manufacturing->start_date;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/manufacturing', 'action');

            event(new Notifications($title));

    }
   
        public function done(Request $request)
    {
        $manufacturing = Manufacturing::find($request->id);    
        if($manufacturing->done == 1)   header( "refresh:3;url=/manufacturing" ); 

        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:equipments'],
            'name' => ['required','max:255', 'unique:equipments'],
            'id' => ['required'],
            'power' => ['required'],
            'group' => ['required'],
            'company' => ['required'],
            'location' => ['required'],
            'end_date' => ['required'],
            ],
            [
               'code.unique' => '   كـــود المُـــعده   موجود  ',
                'code.required' => '  ادخل    كـــود المُـــعده   ',

                'name.unique' => ' إســـــم المُـــعده    موجود  ',
                'name.required' => ' ادخل إســـــم المُـــعده ',

                'id.required' => 'أعد المحاولة مرة أخرى ',
                'power.required' => '  ادخل       السعة / القدرة    ',
                'group.required' => '  ادخل  المجموعة    ',
                'company.required' => '  ادخل    الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',

                'end_date.required' => '  ادخل     تاريخ نهاية التصنيع   ',
                
            ]);

            $equipment = new Equipment;
    
            $equipment->code =$request->code;
            $equipment->name = $request->name;
            $equipment->power = $request->power;
            $equipment->groups = $request->group;
            $equipment->company = $request->company;
            $equipment->location = $request->location;
            $equipment->ownership_date = $request->end_date;
            $equipment->notes = $request->notes;
            $equipment->save();

            $group = DB::table('groups')->select('*')->where('id' ,$request->group)->first();
            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
            $location = DB::table('locations')->select('*')->where('id' ,$request->location)->first();

            $title = 'تم   إنهاء تصنيع  مُعِــــده  ';
            $body =  '  تم إنهاء تصنيع  مُعِــــده   كود  '.$request->code;$body.= "\r\n /";
            $body .=  'اسم   '.$request->name;$body.= "\r\n /";
            $body .=  '  السعة / القدرة  '.$request->power;$body.= "\r\n /";
            $body .=  ' المجموعة  '.$group->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$location->name;$body.= "\r\n /";
            $body .=  '   تاريخ إنهاء التصنيع  '.$request->end_date;$body.= "\r\n /";

            $manufacturing->end_date = $request->end_date;
            $manufacturing->done = 1;
            $manufacturing->save();

            $request->session()->flash('DoneManufacturing', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/manufacturing', 'action');

            event(new Notifications($title));


            return  back();
    }

    public function manufacturingData()
    {
            $manufacturing = Manufacturing::where([ ['is_delete',0], ['done',0] ])->get();
            

            return DataTables::of($manufacturing) 
                ->addColumn('action', function ($manufacture) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('manufacturing', 'write')){
                        $action .='<a  href="/manufacturing/' . $manufacture->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                        $action .='<a  href="/manufacturing/' . $manufacture->id . '" class="edit-button"><i class="fas fa-check-double"></i> </a>';
                    }
                    if($auth->canView('manufacturing', 'delete')){
                        $action .='<a  coords="' . $manufacture->name . '"  id="' . $manufacture->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }


      public function trash()
    {
        return view('manufacturing.trash');
    }
    
        public function manufacturingTrash()
    {
            $manufacturing = Manufacturing::where([ ['is_delete',1] ])->get();
            

            return DataTables::of($manufacturing) 
                ->addColumn('action', function ($manufacture) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('manufacturing', 'delete')){
                        $action .='<a  coords="' . $manufacture->name . '"  id="' . $manufacture->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
        
    }
    public function restore($id)
    {
            $manufacturing = Manufacturing::find($id);    
            $manufacturing->is_delete  = 0;
            $manufacturing->save();

            $title = 'تم استرجاع تصنيع مُـــعده بنجاح';

            $auth = new AuthController();


            $title = 'تم   استرجاع تصنيع  مُعِــــده  ';
            $body =  '  تم استرجاع تصنيع مُعِــــده   كود  '.$manufacturing->code;$body.= "\r\n /";
            $body .=  'اسم   '.$manufacturing->name;$body.= "\r\n /";
            $body .=  '    تاريخ بداية التصنيع  '.$manufacturing->start_date;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/manufacturing', 'action');

            event(new Notifications($title));

    }


}
