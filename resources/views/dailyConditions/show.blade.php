@extends('layout.app')
@section('title', 'تحديث الحالة الفنية ')
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
        .fa-edit{
            font-size: 1.2rem !important;
            color: blue;
        } 
        .fa-trash-alt{
            font-size: 1.2rem !important;
            color: red;
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
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #73a294;
    }
</style>
@endsection
@section('body')

<section class="section">
        <div class="section-header">
            <h1>تحديث الحالة الفنية </h1>
                @if (session('NewUpdate'))
                    <div class="alert alert-success">
                        {{ session('NewUpdate') }}
                    </div>
                @endif
        </div>
        <div class="section-body">
        <form method="post" class="needs-validation" novalidate="" action="/dailyConditions/save" enctype="multipart/form-data"  id="myForm" dir="rtl">
            @csrf
        <table id='table'   dir="rtl" width="100%">
            <thead>
                <tr>
                    <th>كود المُـــعده</th>
                    <th>اسم المُـــعده</th>
                    <th>الحالة الفنية</th>
                    <th>الصيانة</th>
                    <th>اسم القائم بالعمل</th>
                </tr>
            </thead>
            <tbody> 
                @foreach ($equipments as $equipment)
                    <tr>
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="ids[]" value="{{ $equipment->ids }}">
                        <input type="hidden" name="name[]" value="{{ $equipment->id }}">
                        <td>{{ $equipment->code }}</td>
                        <td>{{ $equipment->name }}</td>
                        <td> 
                            <select class="form-control select2" name='conditions[]' required >
                                <option disabled selected value="">أختر   الحالة الفنية </option>
                                @foreach ( $conditions as $condition)
                                    <option value=" {{ $condition->id }}"{{ ($equipment->conditions == $condition->id ? "selected":"") }}> {{ $condition->name}}</option>
                                @endforeach                   
                            </select>  
                        </td>
                            <td><textarea name="notes[]"  cols="50">{{ $equipment->notes }}</textarea></td>
                            <td> 
                            <select class="form-control select2" name='employee[{{ $equipment->id }}][]'    multiple>
                                <option disabled  value="">أختر   اسم القائم بالعمل  </option>
                                @foreach ( $employees as   $employee)
                                @if(isset($equipment->employee))
                                    @if (in_array($employee->id ,$equipment->employee) )
                                        <option value=" {{ $employee->id }}" selected> {{ $employee->name}}</option>
                                    @else
                                        <option value=" {{ $employee->id }}" > {{ $employee->name}}</option>
                                    @endif          
                                @else
                                <option value=" {{ $employee->id }}" > {{ $employee->name}}</option>
                                @endif   
                                @endforeach                   
                            </select>  
                        </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
        <div class="card-footer text-center">
                <button class="btn  btn-primary ">حفظ البيانات</button>
        </div>
        </form>
    </section>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    {{-- <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script> --}}
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="{{ asset('js/select2.full.min.js')}}"></script>

    <script>
        $('#reportDaily'). addClass('active');
        $("#dailyConditions ul.dropdown-menu").css("display", "block");

  $('#myForm').on('submit', function(e){ 
        $('input[type=search]').val('').change();
        $('#table').DataTable().search('').draw();
      var form = $(this);
        e.preventDefault();
        if (form[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
         }else{
        $( ".btn-primary" ).addClass( "btn-progress disabled " );
            setTimeout(function(){
                document.getElementById('myForm').submit();
                    // this.submit();
                },5000);
           
        }
    });
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
        });

    </script>
    @endsection