@extends('layout.app')
@section('title', 'تصريح خروج مُعِــــده')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1>تصريح خروج مُعِــــده (تشغيل السفن)</h1>
            
          </div>
          <div class="section-body">
            @if (session('NewVessel'))
                <div class="alert alert-success">
                    {{ session('NewVessel') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/vessels" enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                    
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   إســـــم المُـــعده   </label>
                            <select class="form-control select2" name='equipment[]' multiple required >
                        @foreach ( $equipments as $equipment)
                            <option value=" {{ $equipment->id }}" {{ (old('equipment') == $equipment->id ? "selected":"") }}> {{ $equipment->name }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     إســـــم المُـــعده    </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('equipment') }}</strong>

                          </div>
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right">     اسم الباخرة   </label>
                            <select class="form-control select2" name='vessel' required >
                        <option disabled selected value="">أختر اسم الباخرة  </option>
                        @foreach ( $vessels as $vessel)
                            <option value=" {{ $vessel->name }}" {{ (old('vessel') == $vessel->name ? "selected":"") }}> {{ $vessel->name }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     اسم الباخرة     </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('vessel') }}</strong>

                          </div>
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> رقم خطة التفريغ / الشحن</label>
                            <input type="text" class="form-control"  name="plan"   value="{{ old('plan') }}"  required="">
                            <div class="invalid-feedback">     ادخل رقم خطة التفريغ / الشحن     </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('plan') }}</strong>

                          </div>
                                                     
                        </div>
 <div class="row">         
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   الشركة التابعة لها   </label>
                            <select class="form-control select2" name='company' required >
                        <option disabled selected value="">أختر الشركة التابعة لها </option>
                        @foreach ( $companies as $company)
                            <option value=" {{ $company->id }}" {{ (old('company') == $company->id ? "selected":"") }}> {{ $company->name_ar }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     الشركة التابعة لها    </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('company') }}</strong>

                          </div>
 <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   مدة التشغيل المتوقعة    </label>
                            <input type="text" class="form-control"  name="duration"   value="{{ old('duration') }}"  required="">
                            <div class="invalid-feedback">     ادخل مدة التشغيل المتوقعة   </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('duration') }}</strong>

                          </div>
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> ملاحظات الفحص قبل التحرك</label>
                            <textarea  class="form-control"  name="details" cols="50"  required></textarea>
                              <div class="invalid-feedback">     ادخل      ملاحظات الفحص قبل التحرك      </div>
                            <strong  class="float-right" style="color: red;">{{ $errors->first('details') }}</strong>

                          </div>
                    <div class="form-group col-md-4 col-12">
                                       <label  class="float-right">اسم السائق</label>
                    <select class="form-control select2" name='driver[]' multiple required >
                        <option disabled  value="">أختر اسم السائق          </option>
                        @foreach ( $drivers as $driver)
                            <option value=" {{ $driver->id }}"> {{ $driver->name }}</option>
                        @endforeach                   
                      </select>             
                        <div class="invalid-feedback">     ادخل     اسم السائق     </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('driver') }}</strong>

                          </div>
                        
                       
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">    تفاصيل أخرى  </label>
                            <input type="text" class="form-control"  name="notes"   value="{{ old('notes') }}" >
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

$('#addVess'). addClass('active');
$("#vessels ul.dropdown-menu").css("display", "block");

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