@extends('layout.app')
@section('title',  ' تقرير الحالة الفنية  ' )
@section('style')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet">
    
    <style>
        .form-inline{
            display: inline !important;
        }
        a{
            text-decoration: none;
        }
        #table_length, #table_paginate, #table_info{
            display: none;
        }
    </style>
@endsection
@section('afterStyle')
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('body')

 <section class="section">
          <div class="section-header">
              <span id="headers" >
            <h1 id="filename">    {{   $date}}</h1>
            </span>
          </div>
           <div class="section-body">
        <table id='table'   dir="rtl" width="100%">
            <thead>
                <tr>
                    <th>كود المُـــعده</th>
                    <th>اسم المُـــعده</th>
                    <th>الحالة الفنية</th>
                    <th>المجموعة</th>
                    <th>الصيانة </th>
                    <th>اسم القائم بالعمل</th>
                </tr>
            </thead>
            <tbody> 
                @foreach ($equipments as $equipment)
                    <tr>
                        <td>{{ $equipment->code }}</td>
                        <td>{{ $equipment->name }}</td>
                        <td> 
                                @foreach ( $conditions as $condition)
                                @if ($equipment->conditions == $condition->id)
                                    {{ $condition->name}}
                                @endif
                                @endforeach                   
                        </td>
                        <td> 
                                @foreach ( $groups as $group)
                                @if ($equipment->group == $group->id)
                                    {{ $group->name}}
                                @endif
                                @endforeach                   
                        </td>
                        <td>{{$equipment->notes  }}</td>
                        <td>{{$equipment->employee  }}</td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>

    </section>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script> 
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> 
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script> --}}
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <script src="{{ asset('js/select2.full.min.js')}}"></script>

    <script>
        $('#reportDaily'). addClass('active');
        $("#dailyConditions ul.dropdown-menu").css("display", "block");
        var html = document.getElementById("headers").innerHTML;
        var filename = document.getElementById("filename").innerHTML;
         // CREATE
        var dt = $('#table').DataTable({
            dom: 'lBfrtip',
            order:[0,'asc'],
            "lengthMenu": [
                [ -1],
                [ "الكل"]
            ],
            responsive: true,
            "language": {
                "searchPlaceholder": "ابحث",
                "sSearch": "",
                "sProcessing": "....جاري التحميل",
                "sLengthMenu": "أظهر مُدخلات _MENU_",
                "sZeroRecords": "لم يُعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مُدخل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجلّ",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                "sInfoPostFix": "",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            },
            
            buttons: [

                {
                    extend: 'print',
                    text: '<i class="fas fa-print"> طباعة',
                    messageTop: '<img src="/images/day.png" style="position:relative;width:100%;" /><br>' +
                        html,
                    autoPrint: true,
                    title:'',
                    exportOptions: {
                        columns: [0, 1,2,3]
                    },
                    customize: function(win) {
                    

                    }
                },  {
            extend: 'excel',
            text: '<i class="fas fa-file-excel"></i> Excel',
            title:'',
            filename: '  تقرير الحالة الفنية للمعدات'+filename,
            messageTop: ' تقرير الحالة الفنية للمعدات'+filename,
            exportOptions: {
                modifier: {
                    page: 'current'
                }
            }
        }
            ]
        });

    </script>
    @endsection