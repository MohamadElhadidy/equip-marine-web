@extends('layout.app')
@section('title', '  رجوع مُـــعده ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1> رجوع   مُـــعده </h1>
            
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
                  <form method="post" class="needs-validation" novalidate="" action="/requests/done/{{ $requestsOut->id }}"  enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <input type="hidden" name="_method" value="put" />
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                              <div class="form-group col-md-4 col-12">
                            <label  class="float-right">اسم المعدة</label>
                            <input type="text" class="form-control" disabled value="{{ $equipment->name }}">
                          </div>
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">اسم السائق</label>
                            <input type="text" class="form-control"   disabled value="{{ $drivers }}"  >

                          </div>
                           <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   الموقع     </label>
                        @foreach ( $locations as $location)
                        @if($requestsOut->location == $location->id )
                            <input type="text" class="form-control"    disabled value="{{ $location->name }}"  >
                            @endif
                        @endforeach                   
 </div>
 </div>
                          
 <div class="row">         
                           
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> ملاحظات الفحص قبل التحرك</label>
                            <textarea  class="form-control"  cols="50"  disabled  >{{ $requestsOut->details }}</textarea>

                          </div>

                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> ملاحظات الفحص بعد الإنتهاء</label>
                            <textarea  class="form-control"  name="after_details" cols="50"  required  ></textarea>
                              <div class="invalid-feedback">     ادخل      ملاحظات الفحص بعد الإنتهاء      </div>
                            <strong  class="float-right" style="color: red;">{{ $errors->first('after_details') }}</strong>

                          </div>
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">    تفاصيل أخرى  </label>
                            <input type="text" class="form-control"  name="after_notes"  >
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