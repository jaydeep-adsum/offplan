<!DOCTYPE html>
</body>
</html>

<!doctype html>
<html>
<head>
    <title>Welcome</title>
</head>
<body style="background:#f3f3f3;padding:0;margin:0;font-family:Arial,sans-serif;letter-spacing:1px;">
    <div style="background:#ffffff;width:700px;margin:20px auto;box-shadow: 0px 25px 47px rgba(0,0,0,0.1);">
        <table style="width:100%;border-collapse:collapse;">
            <tbody>
                <tr>
                    <td style="background:#F6F6F6;padding:15px;vertical-align:middle;border-top:4px solid #1E3F7A"><img style="float:left;" src="" />  <span style="font-size:24px;font-weight:600;color:#6B6B6B;float:left;padding:30px 0;margin-left:15px;">Offplan</span></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;padding:20px 0;">
                        
                        <img src="{{$message->embed(asset('public/files/logo.png'))}}" alt="">
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p style="font-size:14px;">Hey, {{$body['firstName']}}</p>
                        
                        <p style="font-size:14px;line-height:20px;">{{$body['data']}}<br>
                        <b>Thank You!!</b></p>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="text-align:center;padding:20px 0;width:700px;margin:auto;">
        <p style="font-size:14px;">Copyright @2020 <b>Offplan</b> All Rights Reserved. We appreciate you!</p>
        <a style="margin:10px;color:#686363;text-decoration:none;font-weight:bold;" href="#" title="Facebook">support@regen-brokers.com</a>
    </div>
</body>
</html>