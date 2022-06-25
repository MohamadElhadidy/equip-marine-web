@extends('layout.app')
@section('title', ' إنهاء طلب إصلاح    ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1> إنهاء طلب إصلاح</h1>
            
          </div>
          <div class="section-body">
            @if (session('DoneMaintenance'))
                <div class="alert alert-success">
                    {{ session('DoneMaintenance') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/maintenance/done/{{ $maintenance->id }}" enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <input type="hidden" name="_method" value="put" />
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   إســـــم المُـــعده   </label>
                            <input type="text" class="form-control"   disabled   value="{{ $equipment->name}}" >
                          </div>
       

                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> الأعمال التي يجب تنفيذها</label>
                            <textarea  class="form-control"   cols="50"  disabled>{{ $maintenance->details }}</textarea>

                          </div>
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">  الأعمال التي تم تنفيذها </label>
                            <textarea  class="form-control"  name="after_details" cols="50"  required></textarea>
                              <div class="invalid-feedback">     ادخل    الأعمال التي تم تنفيذها       </div>
                            <strong  class="float-right" style="color: red;">{{ $errors->first('after_details') }}</strong>

                          </div>
                           </div>
 <div class="row">
                  


  <div class="form-group col-md-4 col-12">
                            <label  class="float-right">اسم القائم بالصيانة</label>
                    <select class="form-control select2" name='employees[]' multiple required >
                        <option disabled  value="">أختر اسم القائم بالصيانة           </option>
                        @foreach ( $employees as $employee)
                            <option value=" {{ $employee->id }}"> {{ $employee->name }}</option>
                        @endforeach                   
                      </select>             
                                  <div class="invalid-feedback"> ادخل اسم القائم بالصيانة        </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('employees') }}</strong>

                          </div>
                        
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">    تفاصيل أخرى  </label>
                            <input type="text" class="form-control"  name="after_notes"   value="{{ old('notes') }}" >
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

$('#reportMain'). addClass('active');
$("#maintenance ul.dropdown-menu").css("display", "block");

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