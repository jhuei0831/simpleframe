<?php

namespace App\Http\Controller\Auth;

use GUMP;
use Twig\Environment;
use App\Services\Log\Log;
use App\Services\Auth\Password;
use Kerwin\Core\Mail;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Config;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Security;
use Kerwin\Core\Support\Facades\Session;

class RegisterController
{
    /**
     * GUMP驗證後的錯誤訊息
     *
     * @var array
     */
    public $errors = [];
    
    /**
     * 註冊頁面
     *
     * @param  Twig\Environment $twig
     * @return void
     */
    public function index(Environment $twig): void
    {
        echo $twig->render('auth/register.twig');
    }
    
    /**
     * 會員註冊
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Services\Log\Log $log
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function register(Request $request, Log $log, Environment $twig): void
    {
        // 只取要的值，防止被插入不必要的值
        $post = Toolbox::only($request->request->all(), ['token', 'name', 'email', 'password', 'password_confirm']);
        $data = Security::defendFilter($post);
        
        $validation = $this->validation();

        $validData = $validation->run($data);
        if (!$validation->errors()) {
            $checkUser = Database::table('users')->where('email ="'.$validData['email'].'"')->first();
            if ($checkUser) {
                $this->errors = ['信箱已被註冊使用'];
                Message::flash('信箱已被註冊使用', 'error');
            }
            elseif ($request->server->get('AUTH_PASSWORD_SECURITY') === 'TRUE' && (count(Password::rule($validData['password'])) <= 3 || !preg_match('/.{8,}/',$validData['password']))) {
                $this->errors = ['密碼不符合規則，請參考密碼規則並再次確認'];
                Message::flash('密碼不符合規則，請參考密碼規則並再次確認', 'error');
            }
            elseif ($validData['password'] != $validData['password_confirm']) {
                $this->errors = ['密碼要和確認密碼相同'];
                Message::flash('密碼要和確認密碼相同', 'error');
            }
            else {
                unset($validData['password_confirm']);
                $authCode = uniqid(mt_rand());
                $validData['password'] = password_hash($validData['password'], PASSWORD_BCRYPT, ['salt' => 'thiswebsitemadebykerwin']);
                $validData['id'] = Toolbox::UUIDv4();
                $validData['role'] = 2;
                $validData['auth_code'] = $authCode;
                Database::table('users')->insert($validData);
                Session::set('USER_ID', $validData['id']);
                /* 在忘記密碼加入資料 */
                Database::table('password_resets')->insert([
                    'id' => $validData['id'], 
                    'password' => json_encode([$validData['password']]),
                    'password_updated_at' => date('Y-m-d H:i:s'), 
                ], false);
                if ($request->server->get('AUTH_EMAIL_VERIFY') === 'TRUE') {
                    $name = $validData['name'];
                    $id = $validData['id'];
                    include_once('./views/auth/email/content.php');
                    Mail::send($subject, $message, $validData['email'], $validData['name']);
                    Message::flash('註冊成功，請前往註冊信箱收取認證信。', 'success')
                        ->redirect(Config::getAppAddress().'auth/email_verified');
                }
                else {
                    $log->info('註冊成功');
                    Message::flash('註冊成功。', 'success')->redirect(Config::getAppAddress());
                }
            }
        } else {
            $this->errors = $validation->get_readable_errors();
            Message::flash('註冊失敗，請檢查輸入', 'error');
        }
        
        echo $twig->render('auth/register.twig', [
            'errors' => $this->errors,
            'post' => Toolbox::only($data, ['name', 'email'])
        ]);
    }

    /**
     * 表單驗證
     *
     * @return GUMP
     */
    private function validation(): GUMP
    {
        $gump = new GUMP();

        // 輸入驗證
        $gump->validation_rules([
            'name'    => 'required|max_len,30',
            'email'   => 'required|valid_email',
            'password'    => 'required|max_len,30|min_len,8',
            'password_confirm'    => 'required|max_len,30|min_len,8',
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'name'    => 'trim|sanitize_string',
            'email'   => 'trim|sanitize_email',
            'password'    => 'trim',
            'password_confirm'   => 'trim',
        ]);

        // 錯誤訊息
        $gump->set_fields_error_messages([
            'name'              => ['required' => '名稱必填', 'max_len' => '名稱必須小於或等於30個字元'],
            'email'             => ['required' => '電子郵件必填', 'valid_email' => '必須符合電子郵件格式'],
            'role'              => ['required' => '角色必填'],
            'password'          => ['required' => '密碼必填', 'max_len' => '密碼必須小於等於30個字元', 'min_len' => '密碼必須大於等於8個字元'],
            'password_confirm'  => ['required' => '確認密碼必填', 'max_len' => '確認密碼必須小於等於30個字元', 'min_len' => '確認密碼必須大於等於8個字元'],
        ]);

        return $gump;
    }
}
    