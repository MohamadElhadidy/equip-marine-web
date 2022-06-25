@extends('layout.app')
@section('title', ' تقرير الحالة الفنية للمعدات')
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
                .fa-trash-alt, .fa-edit, .fa-print, .fa-plus-square{
            font-size: 1.2rem ;
        }
        .fa-edit{
            color: blue;
        } 
        .fa-trash-alt{
            color: red;
        }
        #table .fa-print{
            color: rgb(70, 69, 0);
        }
        .fa-plus-square{
            color: rgb(9, 190, 78);
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
            <h1>تقرير الحالة الفنية للمعدات</h1>
          </div>
           <div class="section-body">

        <table id='table'   dir="rtl" width="100%">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    @canView('dailyConditions','write')
                    <th>تحديث التقرير</th>
                    <th>عرض التقرير</th>
                    <th></th>
                    @endcanView
                </tr>
            </thead>
        </table>
    </section>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>  --}}
    {{-- <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script> --}}

    {{-- <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script> --}}
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script>
        $('#reportDaily'). addClass('active');
        $("#dailyConditions ul.dropdown-menu").css("display", "block");

        // CREATE
        var dt = $('#table').DataTable({
            dom: 'lBfrtip',
            responsive: true,
            order:[0, 'desc'],
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
            ajax: '/dailyConditionsData',
            columns: [
                 {
                    data: 'date',
                    name: 'date'
                },
                 {
                    data: 'action3',
                    name: 'action3'
                },
                @canView('dailyConditions','write')
                 {
                    data: 'action2',
                    name: 'action2'
                },
                {
                    data: 'action',
                    name: 'action'
                }
                @endcanView
                

            ]
        });
        channel.bind('items', function(data) {
                $('#table').DataTable().ajax.reload();
        });
     function getId(id,name) {
            $.confirm({
                title: name,
                icon: 'fas fa-trash',
                content: 'هل أنت متأكد من عملية الحذف ؟ ',
                type: 'red',
                rtl: true,
                closeIcon: false,
                closeIconClass: 'fas fa-close',
                draggable: true,
                dragWindowGap: 0,
                typeAnimated: true,
                theme: 'supervan',
                autoClose: 'cancelAction|60000',
                buttons: {
                    ok: {
                        text: 'حذف',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                type: "DELETE",
                                url: '/dailyConditions/'+id,
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
                                    content: ' تم حذف ' + name + ' بنجاح  ',
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
                                content: 'تم إلغاء عملية الحذف',
                                icon: 'fa fa-warning',
                            });
                        }
                    },
                }
            });
        }
    </script>
    @endsection