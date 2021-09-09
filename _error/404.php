<?php
    if (session_status() == PHP_SESSION_NONE) {
        $root = "../";
        include($root.'_config/settings.php');
    }
    header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="zh-TW">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 Not Found</title>
        <link rel="icon" href="<?php echo APP_IMG?>favicon.ico">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body>
        <style>
            .gradient {
                background-image: linear-gradient(135deg, #684ca0 35%, #1c4ca0 100%);
            }
        </style>

        <div class="gradient text-white min-h-screen flex items-center">
            <div class="container mx-auto p-4 flex flex-wrap items-center">
                <div class="w-full md:w-5/12 text-center p-4">
                    <img src="https://themichailov.com/img/not-found.svg" alt="Not Found" />
                </div>
                <div class="w-full md:w-7/12 text-center md:text-left p-4">
                    <div class="text-6xl font-medium">404</div><br>
                    <div class="text-xl md:text-3xl font-medium mb-4">
                        頁面不存在
                    </div>
                    <div class="text-lg mb-8">
                        您可能輸入錯誤的網址或頁面可能已經移動
                    </div>
                    <a href="<?php echo APP_ADDRESS?>" class="border border-white rounded p-4">返回首頁</a>
                </div>
            </div>
        </div>
    </body>
</html>