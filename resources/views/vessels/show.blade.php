@extends('layout.app')
@section('title', ' رجوع مُـــعده   ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1> رجوع مُـــعده    (تشغيل السفن)</h1>
            
          </div>
          <div class="section-body">
            @if (session('DoneVessel'))
                <div class="alert alert-success">
                    {{ session('DoneVessel') }}
                </div>
            @endif
          
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/vessels/done/{{ $vessel->id }}" enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <input type="hidden" name="_method" value="put" />
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                      <div class="row">         
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   إســـــم المُـــعده   </label>
                            <input type="text" class="form-control"   disabled   value="{{ $equipments}}" >
                          </div>
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right">     اسم الباخرة   </label>
                            @foreach ( $vessels as $vessel0)
                            @if($vessel->vessel  == $vessel0->name)
                            <input type="text" class="form-control"  disabled value=" {{ $vessel0->name }}"  >
                            @endif
                        @endforeach                   
                          </div>
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> رقم خطة التفريغ / الشحن</label>
                            <input type="text" class="form-control"   disabled   value="{{ $vessel->plan }}" >

                          </div>
                        </div>
                            <div class="row">         
                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   الشركة التابعة لها   </label>
                            @foreach ( $companies as $company)
                            @if($vessel->company  == $company->id)
                            <input type="text" class="form-control"  disabled value=" {{ $company->name_ar }}"  >
                            @endif
                        @endforeach             
                                          

                          </div>
                        <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   مدة التشغيل المتوقعة    </label>
                            <input type="text" class="form-control"  disabled   value="{{ $vessel->duration }} ساعة"  >
                          </div>
                          <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> ملاحظات الفحص قبل التحرك</label>
                            <textarea  class="form-control"   cols="50"  disabled>{{ $vessel->details }}</textarea>

                          </div>
                           </div>
 <div class="row">
                    <div class="form-group col-md-4 col-12">
                            <label  class="float-right">   اسم السائق     </label>
                            <input type="text" class="form-control"     value="{{ $drivers }}"  disabled>

                          </div>


 
                        <div class="form-group col-md-4 col-12">
                            <label  class="float-right"> ملاحظات الفحص بعد الإنتهاء </label>
                            <textarea  class="form-control"  name="after_details" cols="50"  required></textarea>
                              <div class="invalid-feedback">     ادخل      ملاحظات الفحص بعد الإنتهاء      </div>
                            <strong  class="float-right" style="color: red;">{{ $errors->first('after_details') }}</strong>

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

$('#reportVess'). addClass('active');
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