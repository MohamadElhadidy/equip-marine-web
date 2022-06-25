@extends('layout.app')
@section('title', 'المستخدمين')
@section('style')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet">
    
    <style>
        .form-inline{
            display: inline !important;
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
            <h1>المستخدمين</h1>
          </div>
          
        <table id='table'   dir="rtl" width="100%">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>اسم الدخول</th>
                    <th>نوع الحساب</th>
                    <th> </th>
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
            ajax: '/usersData',
            columns: [
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'auth',
                    name: 'auth'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ],
            buttons: [

                {
                    extend: 'print',
                    text: '<i class="fas fa-print"> طباعة',
                    messageTop: '<img src="/images/print_header.png" style="position:relative;width:100%;" />',
                    autoPrint: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    customize: function(win) {
                        $(win.document.body).addClass('body_content');
                        $(win.document.body).find('table th')
                            .css('background', 'rgb(24, 143, 190)')
                            .css('margin', '0px');
                        $(win.document.body)
                            .css('direction', 'rtl !important')
                            .css('width', '100%');
                        $(win.document.body).find('h1')
                            .css('display', 'none');
                        $(win.document.body).find('table tr td')
                            .css('text-align', 'center')
                            .css('font-size', '1.3rem')
                            .css('padding', '5px');
                        $(win.document.body).find('table th')
                            .css('text-align', 'center')
                            .css('color', '#fff')
                            .css('font-size', '1.3rem !important');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit')
                            .css('margin-right', 'auto')
                            .css('margin-left', 'auto');

                    }
                },
            ]
        });
        channel.bind('notifications', function(data) {
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
                                url: '/users/7',
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
                                    content: ' بنجاح  ' + name +  ' تم حذف ',
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