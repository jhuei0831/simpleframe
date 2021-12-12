<?php

    namespace App\Models;

    class Variable 
    {
        public static $logLevels = [
            '100' => 'debug',
            '200' => 'info',
            '250' => 'notice',
            '300' => 'warning',
            '400' => 'error',
            '500' => 'critical',
            '550' => 'alert',
            '600' => 'energency'
        ];

        public static $logMessages = [
            '修改角色',
            '修改設定',
            '刪除角色',
            '忘記密碼',
            '新增權限',
            '新增角色',
            '權限修改',
            '權限刪除',
            '權限新增',
            '登入成功',
            '登入失敗',
            '登出成功',
            '註冊成功',
        ];
    } 