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

        <section class="section" style="direction: rtl">
          <div class="section-header">
            <h1> </h1>
          </div>
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger" style="background-color: #291997  !important;">
                  <i class="fas fa-truck-loading"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <p>معـــدات</p>
                  </div>
                  <div class="card-body">
                    {{ $equipments }}
                  </div>
                </div>
              </div>
            </div>
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger" style="background-color: #e14d14   !important;">
                  <i class="fas fa-hammer"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <p>معـــدات تحت التصنيع</p>
                  </div>
                  <div class="card-body">
                    {{ $manufacturing }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg" style="background-color: #736f71  !important;">
                  <i class="fas fa-warehouse"></i>
                </div> 
                <div class="card-wrap">
                  <div class="card-header">
                    <p>مخازن</p>
                  </div>
                  <div class="card-body">
                    {{ $warehouses }}
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
                    <p>ورش داخلية</p>
                  </div>
                  <div class="card-body">
                    {{ $workshops }}
                  </div>
                </div>
              </div>
            </div>   
             
          </div>
          <div class="row">
            
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning" style="background: #b015a7    !important;">
                  <i class="fas fa-building"></i>
                </div> 
                <div class="card-wrap">
                  <div class="card-header">
                    <p>منشآت</p>
                  </div>
                  <div class="card-body">
                    {{ $buildings }}
                  </div>
                </div>
              </div>
            </div>   
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg" style="background: #007bff   !important;">
                  <i class="fas fa-check-square"></i>
                </div> 
                <div class="card-wrap">
                  <div class="card-header">
                    <p>ساحات</p>
                  </div>
                  <div class="card-body">
                    {{ $sa7a }}
                  </div>
                </div>
              </div>
            </div>             
          </div>
        </section>

@endsection

@section('script')

@endsection