<?php
    $subject = $_ENV['APP_NAME'].'信箱認證信';
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
        <link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    </head>

    <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8; font-family: \'Noto Sans TC\', sans-serif;" leftmargin="0">
        <!--100% body table-->
        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8">
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
                                    <img width="48" src="'.APP_ADDRESS.'src/img/grapes.png" title="logo" alt="'.APP_NAME.'">
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
                                            <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:\'微軟正黑體\',sans-serif;">Dear '.$name.' 您好</h1>
                                            <span
                                                style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                            <p style="color:#455056; font-size:16px;line-height:24px; margin:0;">
                                                恭喜您完成註冊，請點選以下按鈕完成信箱驗證：
                                            </p>
                                            <a href="'.APP_ADDRESS.'auth/check_email_verified/'.$authCode.'/'.$id.'" style="background:#009696;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;-webkit-border-radius: 50px;-moz-border-radius: 50px;border-radius: 50px;">
                                                信箱驗證
                                            </a><br><br><br>
                                            <p style="color:#455056; font-size:16px;line-height:24px; margin:0;">
                                                如果點擊按鈕沒有反應，請複製或點擊下方連結：
                                            </p>
                                            <a href="'.APP_ADDRESS.'auth/check_email_verified/'.$authCode.'/'.$id.'">'.APP_ADDRESS.'auth/check_email_verified/'.$authCode.'/'.$id.'</a><br>
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
                                <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">&copy; <strong>'.APP_ADDRESS.'</strong></p>
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