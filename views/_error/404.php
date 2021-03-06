<?php
    if (session_status() == PHP_SESSION_NONE) {
        $root = "../";
        include($root.'config/settings.php');
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
        <div class="bg-white min-h-screen px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
            <div class="max-w-max mx-auto">
                <main class="sm:flex">
                    <p class="text-4xl font-extrabold text-indigo-600 sm:text-5xl">404</p>
                    <div class="sm:ml-6">
                        <div class="sm:border-l sm:border-gray-200 sm:pl-6">
                            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">頁面不存在
                            </h1>
                            <p class="mt-1 text-base text-gray-500">您可能輸入錯誤的網址或頁面可能已經移動</p>
                        </div>
                        <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                            <a href="<?php echo APP_ADDRESS?>"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                返回首頁
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>