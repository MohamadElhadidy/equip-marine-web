@extends('layout.app')
@section('title', 'تعديل طلب خروج مُعِــــده ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1>تعديل طلب خروج مُعِــــده </h1>
            
          </div>
          <div class="section-body">
            @if (session('EditRequest'))
                <div class="alert alert-success">
                    {{ session('EditRequest') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/requests/{{ $requests->id }}"  enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                     <input type="hidden" name="_method" value="put" />
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                           
                           
                             <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   إســـــم المُـــعده   </label>
                            <select class="form-control select2" name='equipment' required >
                        <option disabled selected value="">أختر إســـــم المُـــعده </option>
                        @foreach ( $equipments as $equipment)
                            <option value=" {{ $equipment->id }}" {{ ($requests->equipment == $equipment->id ? "selected":"") }}> {{ $equipment->name }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     إســـــم المُـــعده    </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('equipment') }}</strong>

                          </div>
                          
                        <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   مدة التشغيل المتوقعة    </label>
                            <input type="text" class="form-control"  name="duration"   value="{{$requests->duration}}"  required="">
                            <div class="invalid-feedback">     ادخل مدة التشغيل المتوقعة   </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('duration') }}</strong>

                          </div>
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   الشركة التابعة لها   </label>
                            <select class="form-control select2" name='company' required >
                        <option disabled selected value="">أختر الشركة التابعة لها </option>
                        @foreach ( $companies as $company)
                            <option value=" {{ $company->id }}"{{ ($requests->company == $company->id ? "selected":"") }}> {{ $company->name_ar }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     الشركة التابعة لها    </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('company') }}</strong>

                          </div>
                        </div>
 <div class="row">         
                        
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right">الغرض من الطلب</label>
                            <input type="text" class="form-control"  name="reason"   value="{{ $requests->reason }}"  required="">
                            <div class="invalid-feedback">     ادخل الغرض من الطلب     </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('reason') }}</strong>

                          </div>
                    <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   اسم الطالب     </label>
                            <select class="form-control select2" name='employee' required >
                        <option disabled selected value="">أختر اسم الطالب   </option>
                        @foreach ( $employees as $employee)
                            <option value=" {{ $employee->id }}" {{ ($requests->employee == $employee->id ? "selected":"") }}> {{ $employee->name }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     اسم الطالب     </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('employee') }}</strong>
 </div>
                
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">    تفاصيل أخرى  </label>
                            <input type="text" class="form-control"  name="notes"   value="{{ $requests->notes}}"  >
                          </div>
                        </div>
                        
                    <div class="card-footer text-center">
                      <button class="btn  btn-primary ">حفظ البيانات</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>

@endsection

@section('script')
  <script src="{{ asset('js/select2.full.min.js')}}"></script>
  <script src="{{ asset('js/daterangepicker.js')}}"></script>
<script>

$('#reportRequ'). addClass('active');
$("#requests ul.dropdown-menu").css("display", "block");

  $('#myForm').on('submit', function(e){
      var form = $(this);
        e.preventDefault();
        if (form[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }else{
        $( ".btn-primary" ).addClass( "btn-progress disabled " );
          this.submit();
        }
    });
</script>
@endsection