@extends('layout.app')
@section('title', 'منظومة المعدات  ')
@section('style')
<style>
  p{
    font-size: 1.3rem;
  }
  .card-body{
    font-size: 1.2rem;
    text-align: center;
  }
  .fa-tools::before {
  content: "\f7d9";
}
  .fa-times-circle, .fa-tools, .fa-running , .fa-check-square{
    font-size: 1.8rem !important
  }
</style>
@endsection

@section('body')

        <section class="section" style="direction: ltr">
          <div class="section-header">
            <h1> </h1>
          </div><!--
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-times-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <p>لا تعمل</p>
                  </div>
                  <div class="card-body">
                    {{ $notWorking }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-tools"></i>
                </div> 
                <div class="card-wrap">
                  <div class="card-header">
                    <p>داخل الصيانة</p>
                  </div>
                  <div class="card-body">
                    {{ $maintanance }}
                  </div>
                </div>
              </div>
            </div>   
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg" style="background-color: #21d54e !important;">
                  <i class="fas fa-running"></i>
                </div> 
                <div class="card-wrap">
                  <div class="card-header">
                    <p>تعمل حاليا</p>
                  </div>
                  <div class="card-body">
                    {{ $working }}
                  </div>
                </div>
              </div>
            </div>  
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg" style="background: #007bff;">
                  <i class="fas fa-check-square"></i>
                </div> 
                <div class="card-wrap">
                  <div class="card-header">
                    <p>جاهزة للعمل</p>
                  </div>
                  <div class="card-body">
                    {{ $ready }}
                  </div>
                </div>
              </div>
            </div>             
          </div>
        -->
        </section>

@endsection

@section('script')

@endsection