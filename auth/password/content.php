<?php
    $subject = $_ENV['APP_NAME'].'密碼重設';
    $message = 
    '<!doctype html>
    <html lang="en-US">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title>Reset Password Email Template</title>
        <meta name="description" content="Reset Password Email Template.">
        <style type="text/css">
            a:hover {text-decoration: underline !important;}
        </style>
    </head>

    <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
        <!--100% body table-->
        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
            style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: \'Open Sans\', sans-serif;">
            <tr>
                <td>
                    <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                        align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="height:80px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <a href="'.APP_ADDRESS.'" title="logo" target="_blank">
                                    <img width="48" src="'.APP_ADDRESS.'src/img/grapes.png" title="logo" alt="logo">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                    style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                    <tr>
                                        <td style="height:40px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 35px;">
                                            <h1 style="color:#1e1e2d; font-weight:800; margin:0;font-size:32px;font-family:\'微軟正黑體\',sans-serif;">Dear '.$name.' 您好</h1>
                                            <span
                                                style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                            <p style="color:#455056; font-size:16px;line-height:24px; margin:0;">
                                                '.APP_NAME.'收到您忘記密碼的申請，請點選以下按鈕進行密碼修改：
                                            </p>
                                            <a href="'.APP_ADDRESS.'auth/password/password_reset.php?auth='.$auth_code.'&id='.$id.'" style="background:#009696;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;-webkit-border-radius: 50px;-moz-border-radius: 50px;border-radius: 50px;">
                                                重設密碼
                                            </a><br><br><br>
                                            <p style="color:#455056; font-size:16px;line-height:24px; margin:0;">
                                                如果點擊按鈕沒有反應，請複製或點擊下方連結：
                                            </p>
                                            <a href="'.APP_ADDRESS.'auth/password/password_reset.php?auth='.$auth_code.'&id='.$id.'">'.APP_ADDRESS.'auth/password/password_reset.php?auth='.$auth_code.'&id='.$id.'</a><br>
                                            <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height:100px;">
                                          <p style="color:#455056;font-family: \'微軟正黑體\'; font-size:13px;line-height:24px; margin:0;">
                                            如果您有任何問題，請與我們聯絡:<br>
                                            <span valign="bottom">E-mail: example@example.com</span>
                                          </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        <tr>
                            <td style="height:20px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">&copy; <strong>'.$_ENV['APP_URL'].'</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:80px;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--/100% body table-->
    </body>

    </html>';
?>