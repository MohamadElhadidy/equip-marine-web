<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>تصريح خروج مُـــعده</title>
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

    <img src="/images/out{{ $request->company }}.png" style="position:relative;width:100%;margin-bottom: 25px;" /> 
<div class="table">
<table>


    <tr>
        <td>التاريخ : {{ $requestOut->created_at }}</td>
        <td>رقم الطلب : {{ $request->id }}</td>
    </tr>
     <tr>
        <td colspan="2"> اسم المعدة : <p> {{ $request->equipment }} </p></td>
        
    </tr>
    <tr>
       <td>مدة التشغيل : <p>{{ $request->duration }} ساعه </p></td>
         <td>موقع العمل : <p>{{ $requestOut->location }}   </p></td>
    </tr>
     <tr>
        <td colspan="2"> ملاحظات الفحص قبل التحرك  : <p> {{ $requestOut->details }} </p></td>
    </tr>
     <tr>
        <td colspan="2">اسم السائق: <p>{{ $requestOut->driver }}  </p></td>
        {{-- <td>التوقيع : ..................</td> --}}
    </tr>
      <tr id='divider'>
        <td colspan="2"> ملاحظات الفحص   بعد الإنتهاء  : <p> {{ $requestOut->after_details }} </p></td>
    </tr>
     <tr>
        <td>توقيع  السائق: ................. </td>
      <td>توقيع  مسئول الحركة: .............. </td>
    </tr>

    <tr>
        <td colspan="2">
            <div class="footer">
                <footer>
                <h3>إصدار : 1 </h3>
                <h3>تاريخ الإصدار : 2020/2/1</h3>
                <h3>F0612</h3>
                </footer>
            </div>
        </td>
    </tr>

</table>
    
            </div>
            
    
</body>

</html>
