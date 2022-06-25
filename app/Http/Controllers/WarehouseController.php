<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use DB;
use App\Events\Notifications;
use DataTables;


class WarehouseController extends Controller
{
    public function __construct()
        {
        $this->middleware("auth");
        $this->middleware("canView:warehouses,write", [
        'only' => [
            'create' ,
            'store' ,
            'edit' ,
            'update' ,
            ]
        ]);
        $this->middleware("canView:warehouses,read", [
        'only' => [
            'index' ,
            'warehousesData'
            ]
        ]);
        $this->middleware("canView:warehouses,delete", [
        'only' => [
            'destroy' ,
            'restore',
            'trash' ,
            'warehousesTrash'
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
        return view('warehouses.report');
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
        
        return view('warehouses.create',[
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
            'code' => ['required', 'max:255' , 'unique:warehouses'],
            'name' => ['required','max:255', 'unique:warehouses'],
            'company' => ['required'],
            'location' => ['required'],
            'capacity' => ['required'],
            'size' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المخزن موجود  ',
                'code.required' => '  ادخل    كـــود المخزن ',

                'name.unique' => ' إســـــم المخزن  موجود  ',
                'name.required' => ' ادخل إســـــم المخزن ',

                'company.required' => '  ادخل   الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'size.required' => '  ادخل   المساحة     ',
                'capacity.required' => ' ادخل متوسط السعة ',
                
            ]);

            $warehouse = new Warehouse;
    
            $warehouse->code =$request->code;
            $warehouse->name = $request->name;
            $warehouse->company = $request->company;
            $warehouse->location = $request->location;
            $warehouse->capacity = $request->capacity;
            $warehouse->size = $request->size;
            $warehouse->notes = $request->notes;
            $warehouse->save();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();

            $title = ' تم   إضافة مخزن جديد ';
            $body =  '  تم إضافة مخزن جديد  كود  '.$request->code;$body.= "\r\n /";
            $body .=  'اسم   '.$request->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$request->location;$body.= "\r\n /";
            $body .=  '   متوسط السعة  '.$request->capacity;$body.= "\r\n /";
            $body .=  '   المساحة  '.$request->size;$body.= "\r\n /";

            $request->session()->flash('NewWarehouse', $title);
            
            $auth = new AuthController();
            $auth->notify(auth()->user()->id, 2, $title, $body, '/warehouses', 'action');

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
    public function edit(Warehouse $warehouse)
    {

        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();

        return view('warehouses.edit',[
            'warehouse' => $warehouse,
            'companies' =>$companies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:255' , 'unique:warehouses,code,'.$warehouse->id],
            'name' => ['required','max:255',  'unique:warehouses,name,'.$warehouse->id],
            'company' => ['required'],
            'location' => ['required'],
            'capacity' => ['required'],
            'size' => ['required'],
            ],
            [
                'code.unique' => '   كـــود المخزن موجود  ',
                'code.required' => '  ادخل    كـــود المخزن ',

                'name.unique' => ' إســـــم المخزن  موجود  ',
                'name.required' => ' ادخل إســـــم المخزن ',

                'company.required' => '  ادخل   الشركة التابعة لها   ',
                'location.required' => '  ادخل   الموقع     ',
                'size.required' => '  ادخل   المساحة     ',
                'capacity.required' => ' ادخل متوسط السعة ',
                
            ]);
            $body = '';
            if($warehouse->code != $request->code){
                    $body .=  '  تم تغيير كود المخزن من '.$warehouse->code. ' الى '.$request->code;
                    $body.= "\r\n /";
            }   
            if($warehouse->name != $request->name) {
                $body .=  '  تم تغيير اسم المخزن من ' . $warehouse->name. ' الى '.$request->name;
                $body.= "\r\n /";
            }
            if($warehouse->company != $request->company) {
                $company1 = DB::table('hr.companies')->select('*')->where('id' ,$warehouse->company)->first();
                $company2 = DB::table('hr.companies')->select('*')->where('id' ,$request->company)->first();
                $body .=  '  تم تغيير   الشركة التابعة لها  من ' . $company1->name_ar. ' الى '.$company2->name_ar;
                $body.= "\r\n /";
            }
            if($warehouse->location != $request->location) {
                $body .=  '  تم تغيير الموقع  من ' . $warehouse->location. ' الى '.$request->location;
                $body.= "\r\n /";
            }
            if($warehouse->size != $request->size) {
                $body .=  '  تم تغيير المساحة  من ' . $warehouse->size. ' الى '.$request->size;
                $body.= "\r\n /";
            }
            if($warehouse->capacity != $request->capacity) {
                $body .=  '  تم تغيير  متوسط السعة  من ' . $warehouse->capacity. ' الى '.$request->capacity;
                $body.= "\r\n /";
            }
 
            $warehouse->code =$request->code;
            $warehouse->name = $request->name;
            $warehouse->company = $request->company;
            $warehouse->location = $request->location;
            $warehouse->size = $request->size;
            $warehouse->capacity = $request->capacity;
            $warehouse->notes = $request->notes;
            $warehouse->save();


            $title = 'تم تعديل المخزن بنجاح';


            $request->session()->flash('EditWarehouse', $title);
            $auth = new AuthController();

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/warehouses', 'action');

        event(new Notifications($title));


        $companies =  DB::table('hr.companies')
                ->select('*')
                ->get();

        
        return  back()->with([
            'warehouse' => $warehouse,
            'companies' =>$companies,
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
            $warehouse = Warehouse::find($id);    
            $warehouse->is_delete  = 1;
            $warehouse->save();

            $title = 'تم حذف المخزن بنجاح';

            $auth = new AuthController();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$warehouse->company)->first();

            $title = 'تم   حذف مخزن  ';
            $body =  '  تم حذف مخزن   كود  '.$warehouse->code;$body.= "\r\n /";
            $body .=  'اسم   '.$warehouse->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$warehouse->location;$body.= "\r\n /";
            $body .=  '   متوسط السعة  '.$warehouse->capacity;$body.= "\r\n /";
            $body .=  '   المساحة  '.$warehouse->size;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/w arehouses', 'action');

            event(new Notifications($title));

    }
   
    public function warehousesData()
    {
            $warehouses = Warehouse::where('is_delete',0)->get();
            

            foreach ($warehouses as  $warehouse) {
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$warehouse->company)->first();
                    $warehouse->company   = $company->name_ar;
                }
                
            return DataTables::of($warehouses) 
                ->addColumn('action', function ($warehouse) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('warehouses', 'write')){
                        $action .='<a  href="/warehouses/' . $warehouse->id . '/edit" class="edit-button"><i class="fas fa-edit"></i> </a>';
                    }
                    if($auth->canView('warehouses', 'delete')){
                        $action .='<a  coords="' . $warehouse->name . '"  id="' . $warehouse->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-alt"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }


    public function trash()
    {
        return view('warehouses.trash');
    }
    
        public function warehousesTrash()
    {
 $warehouses = Warehouse::where('is_delete',1)->get();
            

            foreach ($warehouses as  $warehouse) {
                    $company = DB::table('hr.companies')->select('*')->where('id' ,$warehouse->company)->first();
                    $warehouse->company   = $company->name_ar;
                }
                
            return DataTables::of($warehouses) 
                ->addColumn('action', function ($warehouse) {
                    $action = '';
                    $auth = new AuthController();
                    if($auth->canView('warehouses', 'delete')){
                        $action .='<a  coords="' . $warehouse->name . '"  id="' . $warehouse->id . '" onclick="getId(this.id, this.coords)"  href="#" class="delete-button"><i class="fas fa-trash-restore"></i> </a>';
                    }
                    return $action;
                })->make(true);
            
    }
    public function restore($id)
    {
            $warehouse = Warehouse::find($id);    
            $warehouse->is_delete  = 0;
            $warehouse->save();

            $title = 'تم استرجاع المخزن بنجاح';

            $auth = new AuthController();

            $company = DB::table('hr.companies')->select('*')->where('id' ,$warehouse->company)->first();

            $title = 'تم   استرجاع مخزن  ';
            $body =  '  تم استرجاع مخزن   كود  '.$warehouse->code;$body.= "\r\n /";
            $body .=  'اسم   '.$warehouse->name;$body.= "\r\n /";
            $body .=  ' الشركة التابعة لها  '.$company->name_ar;$body.= "\r\n /";
            $body .=  '   الموقع  '.$warehouse->location;$body.= "\r\n /";
            $body .=  '   متوسط السعة  '.$warehouse->capacity;$body.= "\r\n /";
            $body .=  '   المساحة  '.$warehouse->size;$body.= "\r\n /";

            if($body != null)$auth->notify(auth()->user()->id, 2, $title, $body, '/w arehouses', 'action');

            event(new Notifications($title));

    }

}
