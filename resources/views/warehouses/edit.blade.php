@extends('layout.app')
@section('title', 'تعديل مخزن  ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1>تعديل مخزن  </h1>
            
          </div>
          <div class="section-body">
            @if (session('EditWarehouse'))
                <div class="alert alert-success">
                    {{ session('EditWarehouse') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/warehouses/{{ $warehouse->id }}"  enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                     <input type="hidden" name="_method" value="put" />
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                           
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> كـــود المخزن </label>
                            <input type="text" class="form-control"  name="code"   value="{{$warehouse->code }}"  required="">
                            <div class="invalid-feedback">     ادخل  كـــود المخزن </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('code') }}</strong>

                          </div>
                          
                         <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> إســـــم المخزن </label>
                            <input type="text" class="form-control"  name="name"   value="{{ $warehouse->name }}"  required="">
                            <div class="invalid-feedback">     ادخل  إســـــم المخزن </div>
                        <strong  class="float-right" style="color: red;">{{ $errors->first('name') }}</strong>
                          </div>
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> الموقع  </label>
                            <input type="text" class="form-control"  name="location"   value="{{ $warehouse->location }}"  required="">
                            <div class="invalid-feedback">     ادخل  الموقع  </div>
                        <strong  class="float-right" style="color: red;">{{ $errors->first('location') }}</strong>
                          </div>
                        </div>
  <div class="row">         
      <div class="form-group col-md-4 col-12">
                            <label  class="float-right">  متوسط السعة   </label>
                            <input type="text" class="form-control"  name="capacity"   value="{{ $warehouse->capacity }}"  required="">
                            <div class="invalid-feedback">     ادخل   متوسط السعة   </div>
                        <strong  class="float-right" style="color: red;">{{ $errors->first('capacity') }}</strong>
                          </div>
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> المساحة  </label>
                            <input type="text" class="form-control"  name="size"   value="{{ $warehouse->size }}"  required="">
                            <div class="invalid-feedback">     ادخل  المساحة  </div>
                        <strong  class="float-right" style="color: red;">{{ $errors->first('size') }}</strong>
                          </div>
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   الشركة التابعة لها   </label>
                            <select class="form-control select2" name='company' required >
                        <option disabled selected value="">أختر الشركة التابعة لها </option>
                        @foreach ( $companies as $company)
                            <option value=" {{ $company->id }}"{{ ($warehouse->company == $company->id ? "selected":"") }}> {{ $company->name_ar }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     الشركة التابعة لها    </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('company') }}</strong>

                          </div>
                 

                          
                        
                        </div>

                        <div class="row">
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right">    تفاصيل أخرى  </label>
                            <input type="text" class="form-control"  name="notes"   value="{{ $warehouse->notes}}"  >
                          </div></div>
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

$('#reportWare'). addClass('active');
$("#warehouses ul.dropdown-menu").css("display", "block");

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