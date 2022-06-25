@extends('layout.app')
@section('title', 'تعديل تصريح خروج مُـــعده ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1> تعديل تصريح خروج مُـــعده </h1>
            
          </div>
          <div class="section-body">
            @if (session('OutEditRequest'))
                <div class="alert alert-success">
                    {{ session('OutEditRequest') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/requests/out/{{ $requestsOut->id }}"  enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <input type="hidden" name="_method" value="put" />
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                              <div class="form-group col-md-4 col-12">
                            <label  class="float-right">اسم المعدة</label>
                            <input type="text" class="form-control" disabled value="{{ $equipment->name }}">
                        <strong  class="float-right" style="color: red;">{{ $errors->first('request_id') }}</strong>

                          </div>
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">اسم السائق</label>
                            <input type="text" class="form-control"  name="driver"  value="{{ $requestsOut->driver }}"  required="">
                            <div class="invalid-feedback"> ادخل اسم السائق       </div>
                       <strong  class="float-right" style="color: red;">{{ $errors->first('driver') }}</strong>

                          </div>
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   الموقع     </label>
                            <select class="form-control select2" name='location' required >
                        <option disabled selected value="">أختر الموقع   </option>
                        @foreach ( $locations as $location)
                            <option value=" {{ $location->id }}"{{ ($requestsOut->location == $location->id ? "selected":"") }}> {{ $location->name }}</option>
                        @endforeach                   
                      </select>       
                        <div class="invalid-feedback">     ادخل     الموقع      </div>
                      <strong  class="float-right" style="color: red;">{{ $errors->first('location') }}</strong>
 </div>
 </div>
                          
 <div class="row">         
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> ملاحظات الفحص قبل التحرك</label>
                            <textarea  class="form-control"  name="details" cols="50"  required>{{ $requestsOut->details }}</textarea>
                              <div class="invalid-feedback">     ادخل      ملاحظات الفحص قبل التحرك      </div>
                            <strong  class="float-right" style="color: red;">{{ $errors->first('details') }}</strong>

                          </div>
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">    تفاصيل أخرى  </label>
                            <input type="text" class="form-control"  name="notes" value="{{ $requestsOut->notes }}" >
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