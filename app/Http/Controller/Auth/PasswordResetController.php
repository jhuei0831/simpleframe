<?php

namespace App\Http\Controller\Auth;

use GUMP;
use App\Models\Log\Log;
use Twig\Environment;
use Kerwin\Core\Mail;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Config;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Session;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Security;
use Symfony\Component\HttpFoundation\Request;

class PasswordResetController
{

    /**
     * GUMP驗證後的錯誤訊息
     *
     * @var array
     */
    private $errors = [];
    
    /**
     * 重設密碼頁面
     *
     * @param  \Twig\Environment $twig
     * @param  string $auth
     * @param  string $id
     * @return void
     */
    public function index(Environment $twig, string $auth, string $id): void
    {
        $this->resetVerify($twig, $auth, $id);
        echo $twig->render('auth/password/reset.twig');
    }
    
    /**
     * 獲取重設密碼郵件頁面
     *
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function forgot(Environment $twig): void
    {
        if (!is_null(Session::get('USER_ID'))) {
            echo $twig->render('_error/404.twig');
            exit;
        }
        echo $twig->render('auth/password/forgot.twig');
    }
    
    /**
     * 獲取重設密碼郵件
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \App\Models\Log\Log $log
     * @return void
     */
    public function getResetEmail(Request $request, Log $log): void
    {
        $post = Toolbox::only($request->request->all(), ['token', 'email']);
        $data = Security::defendFilter($post);
        $authCode = Security::defendFilter(uniqid(mt_rand()));
        $user = Database::table('users')->where("email = '{$data['email']}'")->first();
        if (empty($user)) {
            Message::flash('獲取信件失敗', 'error')->redirect(Config::getAppAddress().'auth/password_forgot');
        }
        else {
            $passwordResets = Database::table('password_resets')->where("id = '{$user->id}'")->first(false);
        }

        if (empty($passwordResets)) {
            Database::table('password_resets')->createOrUpdate([
                'token' => $data['token'], 
                'id' => $user->id,
                'password' => json_encode([$user->password]),
                'password_updated_at' => $user->created_at, 
            ]);
            $passwordResets = Database::table('password_resets')->where("id = '{$user->id}'")->first(false);
        }
        // 確認密碼上次更新時間
        if (strtotime('now') < strtotime($passwordResets->password_updated_at.' +1 days')) {
            $passwordResetsPeriod = date('Y-m-d H:i:s', strtotime($passwordResets->password_updated_at.' +1 days'));
            Message::flash('密碼更新時間小於一天，'.$passwordResetsPeriod.'後才可以再次更改。', 'warning')
                ->redirect(Config::getAppAddress());
        }
        else {
            // 放到信中的變數
            $name = $user->name;
            $id = $user->id;
            include_once('./views/auth/password/content.php');
            $mail = Mail::send($subject, $message, $user->email, $user->name);
            if ($mail) {
                Database::table('password_resets')->createOrUpdate([
                    'token' => $data['token'], 
                    'id' => $id, 
                    'password' => isset($passwordResets->password) ? $passwordResets->password : json_encode([$user->password]),
                    'email_token' => $authCode, 
                    'token_valid' => 'Y',
                    'token_updated_at' => date('Y-m-d H:i:s'), 
                    'password_updated_at' => isset($passwordResets->password_updated_at) ? $passwordResets->password_updated_at : $user->created_at, 
                ]);
                $log->info('忘記密碼', ['id' => $id]);
                Message::flash('請前往註冊信箱收取密碼重設信，謝謝。', 'success')->redirect(Config::getAppAddress().'auth/password_forgot');
            }
            else{
                Message::flash('獲取信件失敗', 'error')->redirect(Config::getAppAddress().'auth/password_forgot');
            }
        }
    }

    /**
     * 密碼重設頁面規範
     *
     * @param  \Twig\Environment $twig
     * @param  string $auth
     * @param  string $id
     * @return void
     */
    public function resetVerify(Environment $twig, string $auth, string $id): void
    {
        // 禁止已登入或連結錯誤訪問
        if (!is_null(Session::get('USER_ID')) && empty($auth) && empty($id)) {
            echo $twig->render('_error/404.twig');
            exit;
        }

        // 確認連結資料正確性
        $passwordResets = Database::table('password_resets')
            ->where("id='{$id}' and email_token='{$auth}'")
            ->first();
        
        if (empty($passwordResets)) {
            Message::flash('連結有問題，請確認或重新申請密碼重設信件，謝謝', 'warning')
                ->redirect(Config::getAppAddress().'auth/password_forgot');
        }
        elseif ($passwordResets->token_valid == 'N') {
            Message::flash('密碼重設信已失效，請重新獲取，謝謝。', 'warning')
                ->redirect(Config::getAppAddress().'auth/password_forgot');
        }
        elseif (strtotime('now') > strtotime($passwordResets->token_updated_at.' +30 minutes')) {
            Message::flash('密碼重設信已逾期，請重新獲取，謝謝。', 'warning')
                ->redirect(Config::getAppAddress().'auth/password_forgot');
        }
        elseif (strtotime('now') < strtotime($passwordResets->password_updated_at.' +1 days')) {
            Message::flash('密碼更新時間小於一天，'.date('Y-m-d H:i:s', strtotime($passwordResets->password_updated_at.' +1 days')).'後才可以再次更改。', 'warning')
                ->redirect(Config::getAppAddress());
        }
        else {
            return;
        }
    }

    /**
     * 密碼重設資料驗證、更新
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \App\Models\Log\Log $log
     * @param  string $auth
     * @param  string $id
     * @return void
     */
    public function reset(Environment $twig, Log $log, Request $request,  string $auth, string $id): void
    {
        $post = Toolbox::only($request->request->all(), ['token', 'password', 'password_confirm']);
        $data = Security::defendFilter($post);
        $gump = new GUMP();

        // 輸入驗證
        $gump->validation_rules([
            'password'          => 'required|max_len,30|min_len,8',
            'password_confirm'  => 'required|max_len,30|min_len,8',
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'password'          => 'trim',
            'password_confirm'  => 'trim',
        ]);

        // 錯誤訊息
        $gump->set_fields_error_messages([
            'password'          => ['required' => '密碼必填', 'max_len' => '密碼必須小於等於30個字元', 'min_len' => '密碼必須大於等於8個字元'],
            'password_confirm'  => ['required' => '確認密碼必填', 'max_len' => '確認密碼必須小於等於30個字元', 'min_len' => '確認密碼必須大於等於8個字元'],
        ]);

        $validData = $gump->run($data);

        // 密碼規則驗證
        if (PASSWORD_SECURE === 'TRUE') {
            $safeCheck = self::rule($data['password']);
        }
        $passwordResets = Database::table('password_resets')
            ->where("id='{$id}' and email_token='{$auth}'")
            ->first(false);

        $password = json_decode($passwordResets->password);
        if ($gump->errors()) {
            $this->errors = $gump->get_readable_errors();
            Message::flash('重設密碼失敗，請檢查輸入', 'error');
        }
        elseif (PASSWORD_SECURE === 'TRUE' && (count($safeCheck) <= 3 || !preg_match('/.{8,}/',$validData['password']))) {
            Message::flash('密碼不符合規則，請參考密碼規則並再次確認', 'error');
        }
        elseif ($validData['password'] != $validData['password_confirm']) {
            Message::flash('密碼要和確認密碼相同', 'error');
        }
        elseif (in_array(md5($validData['password']), $password)) {
            Message::flash('密碼不能與前三次相同', 'error');
        }
        else {
            unset($validData['password_confirm']);
            $validData['password'] = md5($validData['password']);
            // 如果密碼有三筆，清除第一筆
            if (count($password) == 3) {
                $shift = array_shift($password);
                array_push($password, $validData['password']);
            }
            else {
                array_push($password, $validData['password']);
            }

            // 更新使用者密碼
            $updateUsers = Database::table('users')
                ->where("id='{$passwordResets->id}'")
                ->update([
                    'token' => $validData['token'],
                    'password' => $validData['password'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // 更新密碼重設資料
            $updatePasswordResets = Database::table('password_resets')
                ->where("id='{$passwordResets->id}'")
                ->update([
                    'password' => json_encode($password),
                    'token_valid' => 'N',
                    'password_updated_at' => date('Y-m-d H:i:s'),
                ], false);
            if ($updateUsers && $updatePasswordResets) {
                $log->info('密碼重設成功');
                Message::flash('密碼重設成功，請使用新密碼登入。', 'success')->redirect(APP_ADDRESS.'auth/login');
            }   
        }
        echo $twig->render('auth/password/reset.twig', [
            'errors' => $this->errors
        ]);
    }

    /**
     * 密碼規則驗證
     *
     * @param string $password
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
    