<?php

    namespace models\Auth;

    use models\Log\Log;
    use Kerwin\Core\Mail;
    use Kerwin\Core\Support\Facades\Auth;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Session;

    class Email
    {              
        /**
         * Log instance
         *
         * @var models\Log\Log
         */
        public $log;

        public function __construct() {
            $this->log = new Log('Email');
        }

        /**
         * 檢查信箱驗證信
         *
         * @param array $request
         * @param string $root
         * @return void
         */
        public function checkVerifyEmail(array $request, $root='../../'): void
        {
            $_GET = Security::defendFilter($request);
            $user = Database::table('users')->where("id = '{$_GET['id']}'")->first();
            
            if (empty($user->id) || is_null($user->updated_at)) {
                include_once($root.'_error/404.php');
                exit;
            }
            elseif (strtotime('now') > strtotime($user->updated_at.' +30 minutes')) {
                Message::flash('驗證信已逾期，請重新獲取，謝謝。', 'warning')->redirect(APP_ADDRESS.'auth/email/verified.php');
            }
            elseif (Auth::id() != $_GET['id'] || $user->auth_code != $_GET['auth']) {
                Message::flash('連結有問題，請確認或重新申請認證信，謝謝。', 'warning')->redirect(APP_ADDRESS.'auth/email/verified.php');
            }
            elseif (!is_null($user->email_varified_at)) {
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
                $this->log->info('信箱驗證成功', ['id' => Session::get('USER_ID')]);
                Message::flash('信箱驗證成功，謝謝。', 'success')->redirect(APP_ADDRESS);
            }
        }

        /**
         * 獲取信箱驗證信
         *
         * @param array $request
         * @return void
         */
        public function getVerifyEmail(array $request): void
        {
            $data = Security::defendFilter($request);
            $authCode = Security::defendFilter(uniqid(mt_rand()));
            $id = Session::get('USER_ID');
            $user = Database::table('users')->where("id = '{$id}'")->first();
            $name = $user->name;
            /* 信件範本 */
            include_once('./content.php');
            $mail = Mail::send($subject, $message, $user->email, $user->name);
            if ($mail) {
                Database::table('users')
                    ->where("id = '{$id}'")
                    ->update([
                        'token' => $data['token'],
                        'auth_code' => $authCode,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                Message::flash('請前往註冊信箱收取認證信，謝謝。', 'success')->redirect(APP_ADDRESS.'auth/email/verified.php');
            }
            else {
                Message::flash('獲取信箱驗證信失敗!', 'error')->redirect(APP_ADDRESS.'auth/email/verified.php');
            }
        }
    }
    