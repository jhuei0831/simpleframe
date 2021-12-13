<?php

namespace App\Services\Auth;

class Password
{        

    /**
     * 密碼規則驗證
     *
     * @param  string $password
     * @return array
     */
    public static function rule(string $password): array
    {
        $safeCheck = array();
        if(preg_match('/(?=.*[a-z])/',$password))
        {
            array_push($safeCheck, '有英文小寫');
        }
        if(preg_match('/(?=.*[A-Z])/',$password))
        {
            array_push($safeCheck, '有英文大寫');
        }
        if(preg_match('/(?=.*[0-9])/',$password))
        {
            array_push($safeCheck, '有數字');
        }
        if(preg_match('/[\Q!@#$%^&*+-\E]/',$password))
        {
            array_push($safeCheck, '有特殊符號');
        }

        return $safeCheck;
    }
}
    