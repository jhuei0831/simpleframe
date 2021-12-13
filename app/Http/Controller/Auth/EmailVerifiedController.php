<?php

namespace App\Http\Controller\Auth;

use Twig\Environment;
use App\Services\Log\Log;
use Kerwin\Core\Mail;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Auth;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Security;
use Kerwin\Core\Support\Facades\Session;

class EmailVerifiedController
{
        
    /**
     * 獲取信箱驗證信頁面
     *
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function index(Environment $twig): void
    {
        /* 檢查是否已經驗證或沒登入 */
        if (is_null(Session::get('USER_ID')) || !empty(Auth::user()->email_varified_at) || EMAIL_VERIFY==='FALSE') {
            echo $twig->render('_error/404.twig');
            exit;
        }
        echo $twig->render('auth/email/verified.twig');
    }

    /**
     * 檢查信箱驗證信
     *
     * @param  \Twig\Environment $twig
     * @param  Log $log
     * @param  string $auth
     * @param  string $id
     * @return void
     */
    public function checkVerifyEmail(Environment $twig, Log $log, string $auth, string $id): void
    {
        $data = Security::defendFilter(['auth' => $auth, 'id' => $id]);
        $user = Database::table('users')
            ->select('id', 'auth_code', 'email_varified_at', 'updated_at')
            ->where("id = '{$data['id']}'")->first();

        if (empty($user->id) || is_null($user->updated_at)) {
            echo $twig->render('_error/404.twig');
            exit;
        }
        elseif (strtotime('now') > strtotime($user->updated_at.' +30 minutes')) {
            Message::flash('驗證信已逾期，請重新獲取，謝謝。', 'warning')->redirect(APP_ADDRESS.'auth/email_verified');
        }
        elseif (Auth::id() != $data['id'] || $user->auth_code != $data['auth']) {
            Message::flash('連結有問題，請確認或重新申請認證信，謝謝。', 'warning')->redirect(APP_ADDRESS.'auth/email_verified');
        }
        elseif (!empty($user->email_varified_at) && !is_null($user->email_varified_at)) {
            Message::flash('已經通過信箱驗證', 'warning')->redirect(APP_ADDRESS);
        }
        else{
            Session::set('USER_ID', $user->id);
            Database::table('users')
                ->where("id = '{$user->id}'")
                ->update([
                    'token' => TOKEN, 
                    'email_varified_at' => date('Y-m-d H:i:s'), 
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $log->info('信箱驗證成功', ['id' => Session::get('USER_ID')]);
            Message::flash('信箱驗證成功，謝謝。', 'success')->redirect(APP_ADDRESS);
        }
    }

    /**
     * 獲取信箱驗證信
     *
     * @param \Kerwin\Core\Request $request
     * @return void
     */
    public function getVerifyEmail(Request $request): void
    {
        $post = Toolbox::only($request->request->all(), ['token', 'email']);
        $data = Security::defendFilter($post);
        $authCode = Security::defendFilter(uniqid(mt_rand()));
        $id = Session::get('USER_ID');
        $user = Database::table('users')->where("id = '{$id}'")->first();
        $name = $user->name;
        /* 信件範本 */
        include_once('./views/auth/email/content.php');
        $mail = Mail::send($subject, $message, $user->email, $user->name);
        if ($mail) {
            Database::table('users')
                ->where("id = '{$id}'")
                ->update([
                    'token' => $data['token'],
                    'auth_code' => $authCode,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            Message::flash('請前往註冊信箱收取認證信，謝謝。', 'success')->redirect(APP_ADDRESS.'auth/email_verified');
        }
        else {
            Message::flash('獲取信箱驗證信失敗!', 'error')->redirect(APP_ADDRESS.'auth/email_verified');
        }
    }
}
    