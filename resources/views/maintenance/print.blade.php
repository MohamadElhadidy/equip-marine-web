<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title> طلب إصلاح مُـــعده</title>
    <style>
        body {
            direction: rtl;
            text-align: center;
            margin: 0 auto;
        }


        .table {
            display: flex;
            justify-content: center;
            flex-flow: wrap;
        }

        table {
            width: 650px;
            justify-content: center;

        }

        .footer {
              display: flex;
                justify-content: center;
           
        }
footer{
            width: 650px;
            display: flex;
              justify-content: space-between;
}

        h2 {
            font-size: 18px
        }

        p {
            text-decoration: underline;
            display: inline;
        }

        .footer h3 {
            font-size: 12px;
        }

        .header {
            display: flex;
            justify-content: center;
        }


        .header h2 {
            text-align: center;
            border: solid 1px black;
            padding: 3px;

        }

        td{
            font-size: 22px;
            font-weight: bold;
            text-align: right;
             padding: 15px 20px;
        }
        tr:first-child td{
            font-size: 18px;
        }
         tr:last-child td{
               border: solid 1px black;
            padding: 0px 20px;
        }
#divider td{
  border-top: solid 1px black;
}
    </style>
</head>

<body>

    <img src="/images/fix1.png" style="position:relative;width:100%;margin-bottom: 25px;" /> 
<div class="table">
<table>


    <tr>
        <td >التاريخ : {{ $maintenance->created_at }}</td>
        <td>رقم طلب إصلاح : {{ $maintenance->id }}</td>
    </tr>
     <tr>
        <td colspan="2"> اسم المعدة : <p> {{ $maintenance->equipment }} </p></td>
        
    </tr>
    {{-- <tr>
       <td>مدة التشغيل : <p>{{ $request->duration }} ساعه </p></td>
         <td>موقع العمل : <p>{{ $requestOut->location }}   </p></td>
    </tr> --}}
     <tr>
        <td colspan="2">    الأعمال  المطلوب تنفيذها    : <p> {{ $maintenance->details }} </p></td>
    </tr>
     <tr>
        {{-- <td colspan="2">اسم السائق: <p>{{ $requestOut->driver }}  </p></td> --}}
        {{-- <td>التوقيع : ..................</td> --}}
    </tr>
      <tr id='divider'>
        <td colspan="2">   تم إصلاح المعدة  وتم استخدام قطع الغيار التالية    : <p> {{ $maintenance->after_details }} </p></td>
    </tr>
     <tr>
        <td  colspan="2">القائم بالصيانة : <p> {{ $maintenance->employees }} </p></td>
    </tr>
     <tr>
        <td>  مدير الصيانة : ................. </td>
      <td>  مسئول الحركة: .............. </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="footer">
                <footer>
                <h3>إصدار : 2 </h3>
                <h3>تاريخ الإصدار : 2019/4/1</h3>
                <h3>F0604</h3>
                </footer>
            </div>
        </td>
    </tr>

</table>
    
            </div>
            
    
</body>

</html>
