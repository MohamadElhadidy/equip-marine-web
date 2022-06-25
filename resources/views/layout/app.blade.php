<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title> @yield('title')</title>
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">

  @yield('style')
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="{{ asset('js/jquery.min.js')}}"></script>
      <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
      <script>
          Pusher.logToConsole = true;
          var pusher = new Pusher('304e1526249863289cb1', {cluster: 'eu'});
          var channel = pusher.subscribe('store-channel');
            channel.bind('notifications', function(data) {
              setTimeout(function(){  
                  $( "#notifications" ).load(window.location.href + " #notifications" );
                }, 1000)
            });
      </script>
<style>
  .dropdown-item-desc, .dropdown-header{
    text-align: center;
  }
        input[type='text'], input[type='password'], h4, .invalid-feedback, select, option, .section-header, .select2, .select2-results__option, .card-header{
          text-align: center;
      }
      .card .card-header{
        display: block !important;
      }
        .section-header{
        display: block !important;
      }
      .alert.alert-success{
      background-color: #0d751e !important;
        text-align: center;
      }
</style>
@yield('afterStyle')
<style>
  .alert.alert-success {
  background-color: #09c196 !important;
}
</style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
      
        </form>
        <ul class="navbar-nav navbar-right">
          @canView('notifications','read')
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">
                      الاشعارات
              </div>
              <div class="dropdown-list-content dropdown-list-icons" id="notifications">
                @foreach ($notifications as $notification)
                    <a href="{{ $notification->url }}" class="dropdown-item">
                  <div class="dropdown-item-icon  text-white rounded-circle mr-1">
                        <img alt="image"  src="{{ asset( $notification->user->image) }}" width="50">
                </div>
                  <div class="dropdown-item-desc text-center">
                    <h6>{{ $notification->user->name }}</h6>
                    {{ $notification->title }}
                    <div class="time">{{ $notification->created_at }}</div>
                  </div>
                </a>
                @endforeach
                
              </div>
              <div class="dropdown-footer text-center">
                
                <a href="/notifications">أظهر الكل <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          @endcanView
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user" >
            <span id="profile1">
            <img alt="image"  src="{{ asset(auth()->user()->image) }}" class="rounded-circle mr-1">
            </span>
            <div class="d-sm-none d-lg-inline-block"  id="profile3">{{ auth()->user()->name }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title"></div>
              @if ( auth()->user()->auth == '1')
              <a href="/users/create" class="dropdown-item has-icon">
                <i class="fas fa-plus"></i> إنشاء حساب جديد 
              </a> 
              <a href="/users" class="dropdown-item has-icon">
                <i class="fas fa-user-friends"></i> المستخدمين   
              </a> 
              @endif
              <a href="/users/{{ auth()->user()->id }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> الحساب الشخصي
              </a>
              <div class="dropdown-divider"></div>
              <a href="/logout" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="/"> <img src="{{ asset('images/brand.png') }}" ></a>
            {{-- <a href="/">Marine Company</a> --}}
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="/">Marine</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header"></li>
            <li class="dropdown" id="equipments">
              <a href="/" class="nav-link "><i class="fas fa-chart-line"></i><span>الرئيسية</span></a>
            </li>
            <li class="menu-header"></li>
            @canView('equipments','read')
                <li class="dropdown" id="equipments">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-truck-loading"></i><span>المعدات</span></a>
              <ul class="dropdown-menu">
                @canView('equipments','write')
                <li id="addEquip"><a class="nav-link" id="add" href="/equipments/create">إضافة معدة</a></li>
                @endcanView
                <li id="reportEquip"><a class="nav-link" id="report" href="/equipments">تقرير المعدات</a></li>
                @canView('equipments','delete')
                <li id="trashEquip"><a class="nav-link"  id="trash" href="/equipments/trash">تقرير المحذوف</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
            @canView('manufacturing','read')
            <li class="dropdown" id="manufacturing">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-hammer"></i><span>تصنيع معدات</span></a>
              <ul class="dropdown-menu">
                @canView('manufacturing','write')
                <li  id="addManu"><a class="nav-link" id="add" href="/manufacturing/create">تصنيع معدة جديدة</a></li>
                @endcanView
                <li id="reportManu"><a class="nav-link" id="report"  href="/manufacturing">تقرير معدات تحت التصنيع</a></li>
                @canView('manufacturing','delete')
                <li id="trashManu"><a class="nav-link"  id="trash"  href="/manufacturing/trash">تقرير المحذوف</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
              @canView('warehouses','read')
            <li class="dropdown" id='warehouses'>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-warehouse"></i><span>المخازن</span></a>
              <ul class="dropdown-menu">
                @canView('warehouses','write')
                <li id="addWare"><a class="nav-link" id="add" href="/warehouses/create">إضافة مخزن</a></li>
                @endcanView
                <li id="reportWare"><a class="nav-link"  id="report" href="/warehouses">تقرير المخازن</a></li>
                @canView('warehouses','delete')
                <li id="trashWare"><a class="nav-link"   id="trash" href="/warehouses/trash">تقرير المحذوف</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
            @canView('buildings','read')
            <li class="dropdown" id="buildings">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-building"></i><span>المنشآت  / الساحات</span></a>
              <ul class="dropdown-menu">
                @canView('buildings','write')
                <li  id="addBuild"><a class="nav-link" id="add" href="/buildings/create"> إضافة  منشآة / ساحه</a></li>
                @endcanView
                <li id="reportBuild"><a class="nav-link" id="report"  href="/buildings">تقرير المنشآت  / الساحات</a></li>
                @canView('buildings','delete')
                <li id="trashBuild"><a class="nav-link"  id="trash"  href="/buildings/trash">تقرير المحذوف</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
            @canView('workshops','read')
            <li class="dropdown" id="workshops">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-wrench"></i><span>الورش الداخلية</span></a>
              <ul class="dropdown-menu">
                @canView('workshops','write')
                <li   id="addWork"><a class="nav-link"  id="add"href="/workshops/create">إضافة ورشة داخلية</a></li>
                @endcanView
                <li  id="reportWork"><a class="nav-link" id="report"  href="/workshops">تقرير الورش الداخلية</a></li>
                @canView('workshops','delete')
                <li id="trashWork"><a class="nav-link"  id="trash"  href="/workshops/trash">تقرير المحذوف</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
          </ul>
       <!-- <ul class="sidebar-menu">
            <li class="menu-header"></li>
            @canView('dailyConditions','read')
                <li class="dropdown" id="dailyConditions">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-truck-loading"></i><span>الحالة الفنية </span></a>
              <ul class="dropdown-menu">
                @canView('dailyConditions','write')
                <li id="addDaily"><a class="nav-link" id="add" href="/dailyConditions/create">إضافة تقرير يومي</a></li>
                @endcanView
                <li id="reportDaily"><a class="nav-link" id="report"  href="/dailyConditions">تقرير الحالة الفنية</a></li>
                @canView('dailyConditions','delete')
                <li id="trashDaily"><a class="nav-link"  id="trash"  href="/dailyConditions/trash">تقرير المحذوف</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
            @canView('requests','read')
                <li class="dropdown" id="requests">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-arrows-alt-h"></i><span>الطلبات</span></a>
              <ul class="dropdown-menu">
                @canView('requests','write')
                <li id="addRequ"><a class="nav-link" id="add" href="/requests/create"> طلب خروج معدة</a></li>
                @endcanView
                <li id="reportRequ"><a class="nav-link" id="report"  href="/requests">الطلبات</a></li>
                @canView('requests','write')
                <li id="outRequ"><a class="nav-link"   id="trash" href="/requestOut">تصاريح الخروج</a></li>
                @endcanView
              </ul>
            </li>
            @endcanView
            @canView('vessels','read')
                <li class="dropdown" id="vessels">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-ship"></i></i><span>تشغيل السفن</span></a>
              <ul class="dropdown-menu">
                @canView('vessels','write')
                <li id="addVess"><a class="nav-link"    id="add"href="/vessels/create"> تصريح خروج معدة</a></li>
                @endcanView
                <li id="reportVess"><a class="nav-link"  id="report" href="/vessels">تصاريح الخروج</a></li>
              </ul>
            </li>
            @endcanView
             @canView('maintenance','read')
                <li class="dropdown" id="maintenance">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-cogs"></i><span>الصيانة</span></a>
              <ul class="dropdown-menu">
                @canView('maintenance','write')
                <li id="addMain"><a class="nav-link"  id="add"href="/maintenance/create">طلب إصلاح معدة</a></li>
                @endcanView
                <li id="reportMain"><a class="nav-link"  id="report" href="/maintenance">طلبات الصيانة</a></li>
              </ul>
            </li>
            @endcanView
          </ul>-->
          </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
      @yield('body')
      </div>
      <footer class="main-footer">
        <div class="footer-left">
            &copy; IT DEPARTMENT  @php  echo date("Y"); @endphp 
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('js/popper.js')}}"></script>
  <script src="{{ asset('js/tooltip.js')}}"></script>
  <script src="{{ asset('js/bootstrap.min.js')}}"></script>
  <script src="{{ asset('js/jquery.nicescroll.min.js')}}"></script>
  <script src="{{ asset('js/moment.min.js')}}"></script>
  <script src="{{ asset('js/stisla.js')}}"></script>
    
  <!-- Template JS File -->
  
  <script src="{{ asset('js/scripts.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>
   <script>
  

  
  </script>
  @yield('script')
</body>
</html>