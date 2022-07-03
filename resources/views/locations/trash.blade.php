@extends('layout.app')
@section('title', 'تقرير المحذوف')
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
        .fa-trash-restore{
            font-size: 1.2rem !important;
            color: rgb(2, 173, 11);
        }
    </style>
@endsection
@section('afterStyle')
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('body')

 <section class="section">
          <div class="section-header">
            <span id="headers">
            <h1>تقرير المحذوف</h1>
            </span>
          </div>
           <div class="section-body">

        <table id='table'   dir="rtl" width="100%">
            <thead>
                <tr>
                    <th>كود </th>
                    <th>اسم </th>
                    <th>النوع</th>
                    <th>الشركة التابعة لها</th>
                    <th>الموقع</th>
                    <th>الملكية</th>
                    <th>تاريخ التعاقد / الشراء</th>
                    <th>تفاصيل أخرى</th>
                    @canView('locations','delete')
                    <th></th>
                    @endcanView
                </tr>
            </thead>
        </table>
    </section>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script> 
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

    {{-- <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script> --}}
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script>
        $('#trashLoc'). addClass('active');
        $("#locations ul.dropdown-menu").css("display", "block");
        var html = document.getElementById("headers").innerHTML;
        // CREATE
        var dt = $('#table').DataTable({
            dom: 'lBfrtip',
            responsive: true,
            columnDefs: [{
                orderable: true,
                targets: 3
            }],
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
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "الكل"]
            ],
            processing: true,
            serverSide: true,
            //bLengthChange: false,
            ajax: '/locationsTrash',
            columns: [
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'ownership',
                    name: 'ownership'
                },
                {
                    data: 'ownership_date',
                    name: 'ownership_date'
                },
                {
                    data: 'notes',
                    name: 'notes'
                },
                @canView('locations','delete')
                {
                    data: 'action',
                    name: 'action'
                }
                @endcanView
                

            ],
            buttons: [

                {
                    extend: 'print',
                    text: '<i class="fas fa-print"> طباعة',
                    messageTop: '<img src="/images/build.png" style="position:relative;width:100%;" /><br>' +
                        html,
                    autoPrint: true,
                    title:'',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5]
                    },
                    customize: function(win) {
                       
                    }
                },
            ]
        });
        channel.bind('items', function(data) {
                $('#table').DataTable().ajax.reload();
        });
     function getId(id,name) {
            $.confirm({
                title: name,
                icon: 'fas fa-trash-restore',
                content: 'هل أنت متأكد من عملية الاسترجاع ؟ ',
                type: 'green',
                rtl: true,
                closeIcon: false,
                closeIconClass: 'fas fa-trash-restore',
                draggable: true,
                dragWindowGap: 0,
                typeAnimated: true,
                theme: 'supervan',
                autoClose: 'cancelAction|60000',
                buttons: {
                    ok: {
                        text: 'استرجاع',
                        btnClass: 'btn-green',
                        action: function() {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                type: "DELETE",
                                url: '/locations/restore/'+id,
                                error: function() {
                                    $.alert({
                                        title: '',
                                        type: 'red',
                                        content: 'اعد المحاولة مرة أخرى',
                                        icon: 'fa fa-warning',
                                    });
                                }
                            }).done(function(data) {
                                $.alert({
                                    title: '',
                                    type: 'green',
                                    content: ' تم استرجاع ' + name + ' بنجاح  ',
                                    icon: 'fa fa-thumbs-up',
                                });
                                $('#table').DataTable().ajax.reload();
                            });

                        }
                    },
                    cancelAction: {
                        text: 'لا',
                        action: function() {
                            $.alert({
                                title: '',
                                type: 'red',
                                content: 'تم إلغاء عملية الاسترجاع',
                                icon: 'fa fa-warning',
                            });
                        }
                    },
                }
            });
        }
    </script>
    @endsection