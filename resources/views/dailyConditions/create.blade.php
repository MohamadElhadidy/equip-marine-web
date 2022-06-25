@extends('layout.app')
@section('title', 'إضافة تقرير يومي جديد ')
@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">

@endsection

@section('body')

      <section class="section">
          <div class="section-header">
            <h1>إضافة تقرير يومي  جديد</h1>
            
          </div>
          <div class="section-body">
            @if (session('NewDaily'))
                <div class="alert alert-success">
                    {{ session('NewDaily') }}
                </div>
                 <script>
                  setTimeout(function () {
                      window.location.href= '/dailyConditions'; 
                  },3000);
                </script>
            @endif
           
            <div class="row mt-sm-4">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form method="post" class="needs-validation" novalidate="" action="/dailyConditions" enctype="multipart/form-data"  id="myForm" dir="rtl">
                    @csrf
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <div class="row">         
                          

                            <div class="form-group col-md-4 col-12">
                            <label  class="float-right">  التاريخ     </label>
                          <input type="text" name="date"  class="form-control datepicker">
                            <div class="invalid-feedback">     ادخل  التاريخ     </div>
                        <strong  class="float-right" style="color: red;">{{ $errors->first('date') }}</strong>

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

$('#addDaily'). addClass('active');
$("#dailyConditions ul.dropdown-menu").css("display", "block");

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