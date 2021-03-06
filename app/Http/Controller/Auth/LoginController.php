<?php

namespace App\Http\Controller\Auth;

use Twig\Environment;
use App\Services\Log\Log;
use Kerwin\Captcha\Captcha;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Config;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Session;
use Kerwin\Core\Support\Facades\Security;

class LoginController
{

    /**
     * 登入頁面
     *
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function index(Environment $twig)
    {
        if (Session::get('USER_ID')) {
            echo $twig->render('_error/404.twig');
            exit;
        }
        echo $twig->render('auth/login.twig');
    }
            
    /**
     * 圖片驗證碼
     *
     * @return void
     */
    public function captcha(): void
    {
        //設置定義為圖片
        header("Content-type: image/PNG");

        $captcha = new Captcha($_ENV['APP_FOLDER'].'_attributes');
        $captcha->getImageCode(1,5,130,30);
    }

    /**
     * 會員登入
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Services\Log\Log $log
     * @return void
     */
    public function login(Request $request, Log $log): void
    {
        // 只取要的值，防止被插入不必要的值
        $post = Toolbox::only($request->request->all(), ['token', 'email', 'password', 'captcha']);
        $data = Security::defendFilter($post);
        
        $user = Database::table('users')->where('email ="'.$data['email'].'"')->first();
        if ($data['captcha'] != Session::get('captcha')) {
            Message::flash('驗證碼錯誤', 'error')->redirect(Config::getAppAddress().'auth/login');
        } 
        elseif ($user && password_verify($data['password'], $user->password) && empty($user->email_varified_at) && EMAIL_VERIFY === 'TRUE') {
            Session::set('USER_ID', $user->id);
            $log->warning('登入成功，尚未完成信箱驗證');
            Message::flash('登入成功，尚未完成信箱驗證', 'warning')->redirect(Config::getAppAddress().'auth/email_verified');
        } 
        elseif ($user && password_verify($data['password'], $user->password)) {
            Session::set('USER_ID', $user->id);
            $log->info('登入成功');
            Message::flash('登入成功', 'success')->redirect(Config::getAppAddress());
        } 
        else {
            $log->error('登入失敗', ['account' => $data['email']]);
            Message::flash('登入失敗', 'error')->redirect(Config::getAppAddress().'auth/login');
        }
    }
    
    /**
     * 會員登出
     *
     * @param \App\Services\Log\Log $log
     * @return void
     */
    public function logout(Log $log): void
    {
        $log->info('登出成功');
        Session::remove('USER_ID');
        Message::flash('登出成功', 'success')->redirect(APP_ADDRESS.'auth/login');
    }
}
    