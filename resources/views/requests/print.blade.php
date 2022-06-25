<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>طلب خروج مُـــعده</title>
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

    </style>
</head>

<body>

    <img src="/images/outb{{ $request->company }}.png" style="position:relative;width:100%;margin-bottom: 25px;" /> 
<div class="table">
<table>


    <tr>
        <td>التاريخ : {{ $request->created_at }}</td>
        <td>رقم الطلب : {{ $request->id }}</td>
    </tr>
     <tr>
        <td colspan="2"> اسم المعدة : <p> {{ $request->equipment }}</td>
        
    </tr>
     <tr>
        <td>الغرض من الطلب : <p> {{ $request->reason }}</td>
        <td>مدة التشغيل : <p>{{ $request->duration }} ساعه </td>
    </tr>
     <tr>
        <td>اسم الطالب: <p>{{ $request->employee }} </td>
        <td>التوقيع : ..................</td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="footer">
                <footer>
                <h3>إصدار : 1 </h3>
                <h3>تاريخ الإصدار : 2020/2/1</h3>
                <h3>F0611</h3>
                </footer>
            </div>
        </td>
    </tr>

</table>
    
            </div>
            
    
</body>

</html>
