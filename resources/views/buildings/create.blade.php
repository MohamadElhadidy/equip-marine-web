@extends('layout.app')
@section('title', ' إضافة  منشآة / ساحه جديدة')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
                       <h1> إضافة  منشآة / ساحه جديدة</h1>
            
          </div>
          <div class="section-body">
            @if (session('NewBuilding'))
                <div class="alert alert-success">
                    {{ session('NewBuilding') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/buildings" enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                           
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> كـــود المنشآة </label>
                            <input type="text" class="form-control"  name="code"   value="{{ old('code') }}"  required="">
                            <div class="invalid-feedback">     ادخل  كـــود المنشآة </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('code') }}</strong>

                          </div>
                          
                         <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> إســـــم المنشآة </label>
                            <input type="text" class="form-control"  name="name"   value="{{ old('name') }}"  required="">
                            <div class="invalid-feedback">     ادخل  إســـــم المنشآة </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('name') }}</strong>
                          </div>
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> الموقع    </label>
                            <input type="text" class="form-control"  name="location"   value="{{ old('location') }}"  required="">
                            <div class="invalid-feedback">     ادخل     الموقع  </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('location') }}</strong>

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
                            <label  class="float-right">  الملكية     </label>
                            <select class="form-control select2" name='ownership' required >
                        <option disabled selected value="">أختر الملكية   </option>
                            <option value="ملك الشركة" {{ (old('ownership') == 'ملك الشركة' ? "selected":"") }}> ملك الشركة</option>
                            <option value="ايجار" {{ (old('ownership') == 'ايجار' ? "selected":"") }}> ايجار</option>
                      </select>       
                        <div class="invalid-feedback">     ادخل     الملكية      </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('ownership') }}</strong>

                          </div>
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right">  تاريخ التعاقد / الشراء</label>
                          <input type="text" name="ownership_date"  class="form-control datepicker">
                            <div class="invalid-feedback">     ادخل  تاريخ التعاقد / الشراء  </div>
                        <strong  class="float-right" style="color: red;">{{ $errors->first('ownership_date') }}</strong>

                          </div>
                        </div>
                        <div class="row">         
                          
                
                        
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

$('#addBuild'). addClass('active');
$("#buildings ul.dropdown-menu").css("display", "block");

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