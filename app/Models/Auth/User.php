<?php

    namespace App\Models\Auth;

    use GUMP;
    use App\Models\Model;
    use App\Models\Auth\Password;
    use App\Models\Log\Log;
    use Kerwin\Core\Mail;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Config;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Request;
    use Kerwin\Core\Support\Facades\Session;

    class User extends Model
    {  
        /**
         * GUMP驗證後的錯誤訊息
         *
         * @var array
         */
        public $errors = [];

        /**
         * Log instance
         *
         * @var App\Models\Log\Log
         */
        public $log;
                
        /**
         * Request instance
         *
         * @var Kerwin\Core\Support\Facades\Request
         */
        public $request;

        public function __construct() {
            $this->log = new Log('User');
			$this->request = Request::createFromGlobals();
		}

        /**
         * 新增使用者
         *
         * @param array $request
         * @return array
         */
        public function create(array $request): array
        {
            $data = Security::defendFilter($request);
            
            $validation = $this->validation();

            $validData = $validation->run($data);

            if (!$validation->errors()) {
                $checkUser = Database::table('users')->where('email ="'.$validData['email'].'"')->first();
                // 密碼規則驗證
                if (PASSWORD_SECURE === 'TRUE') {
                    $safeCheck = Password::rule($validData['password']);
                }
                if ($checkUser) {
                    return ['msg' => '信箱已被註冊使用', 'type' => 'error'];
                }
                elseif (PASSWORD_SECURE === 'TRUE' && (count($safeCheck) <= 3 || !preg_match('/.{8,}/',$validData['password']))) {
                    return ['msg' => '密碼不符合規則，請參考密碼規則並再次確認', 'type' => 'error'];
                }
                elseif ($validData['password'] != $validData['password_confirm']) {
                    return ['msg' => '密碼要和確認密碼相同', 'type' => 'error'];
                }
                else {
                    unset($validData['password_confirm']);
                    $authCode = uniqid(mt_rand());
                    $validData['password'] = md5($validData['password']);
                    $validData['id'] = Toolbox::UUIDv4();
                    $validData['auth_code'] = $authCode;
                    /* 在忘記密碼加入資料 */
                    Database::table('password_resets')->insert([
                        'id' => $validData['id'], 
                        'password' => json_encode([$validData['password']]),
                        'password_updated_at' => date('Y-m-d H:i:s'), 
                    ], false);
                    Database::table('users')->insert($validData, TRUE);
                    $this->log->info('新增使用者', ['id' => $validData['id']]);
                    return ['msg' => '新增成功。', 'type' => 'success', 'redirect' => Config::getAppAddress().'manage/users'];
                }
            } else {
                $this->errors = $validation->get_readable_errors();
                return ['msg' => '新增失敗，請檢查輸入', 'type' => 'error'];
            }
        }

        /**
         * 使用者刪除
         *
         * @param  string $id
         * @return array
         */
        public function delete(string $id): array
        {
            Database::table('users')->where("id='{$id}'")->delete();
            $this->log->info('刪除使用者', ['id' => $id]);
            return ['msg' => '刪除成功，謝謝。', 'type' => 'success', 'redirect' => Config::getAppAddress().'manage/users'];
        }

        /**
         * 使用者修改
         *
         * @param  array $request
         * @param  string $id
         * @return void
         */
        public function edit(array $request, string $id): void
        {
            $data = Security::defendFilter($request);
            if ($data['type'] == 'profile') {
                unset($data['type']);
                $gump = new GUMP();

                // 輸入驗證
                $gump->validation_rules([
                    'name'    => 'required|max_len,30',
                    'email'   => 'required|valid_email',
                    'role'    => 'required'
                ]);

                // 輸入格式化
                $gump->filter_rules([
                    'name'    => 'trim|sanitize_string',
                    'email'   => 'trim|sanitize_email',
                ]);

                // 錯誤訊息
                $gump->set_fields_error_messages([
                    'name'   => ['required' => '名稱必填', 'max_len' => '名稱必須小於或等於30個字元'],
                    'email'  => ['required' => '電子郵件必填', 'valid_email' => '必須符合電子郵件格式'],
                    'role'   => ['required' => '角色必填'],
                ]);

                $validData = $gump->run($data);

                if (!$gump->errors()) {
                    Database::table('users')->where("id = '{$id}'")->update($validData);
                    $this->log->info('修改使用者資料', ['id' => $id, 'data' => Toolbox::except($validData, 'token')]);
                    Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
                } else {
                    $this->errors = $gump->get_readable_errors();
                    Message::flash('修改失敗，請檢查輸入。', 'error');
                }
            } else {
                unset($data['type']);
                $gump = new GUMP();

                // 輸入驗證
                $gump->validation_rules([
                    'password'    => 'required|max_len,30|min_len,8',
                    'password_confirm'    => 'required|max_len,30|min_len,8',
                ]);

                // 錯誤訊息
                $gump->set_fields_error_messages([
                    'password'          => [
                        'required' => '密碼必填',
                        'max_len'  => '密碼必須小於等於30個字元',
                        'min_len'  => '密碼必須大於等於8個字元'
                    ],
                    'password_confirm'  => [
                        'required' => '確認密碼必填',
                        'max_len'  => '確認密碼必須小於等於30個字元',
                        'min_len'  => '確認密碼必須大於等於8個字元'
                    ],
                ]);

                // 輸入格式化
                $gump->filter_rules([
                    'password'    => 'trim',
                    'password_confirm'   => 'trim',
                ]);
                $validData = $gump->run($data);

                if ($data['password'] != $data['password_confirm']) {
                    Message::flash('密碼要和確認密碼相同!。', 'error');
                } elseif ($gump->errors()) {
                    $this->errors = $gump->get_readable_errors();
                    Message::flash('修改失敗，請檢查輸入。', 'error');
                } else {
                    unset($validData['password_confirm']);
                    $validData['password'] = md5($validData['password']);
                    Database::table('users')->where("id = '{$id}'")->update($validData);
                    $this->log->info('修改使用者密碼', ['id' => $id]);
                    Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
                }
            }
        }     

        /**
         * login
         *
         * @param array $request
         * @return array
         */
        public function login(array $request): array
        {
            $data = Security::defendFilter($request);
            $user = Database::table('users')->where('email ="'.$data['email'].'" and password ="'.md5($data['password']).'"')->first();
            if ($data['captcha'] != Session::get('captcha')) {
                return ['msg' => '驗證碼錯誤', 'type' => 'error', 'redirect' => Config::getAppAddress().'auth/login.php'];
            } 
            elseif ($user && empty($user->email_varified_at) && EMAIL_VERIFY === 'TRUE') {
                Session::set('USER_ID', $user->id);
                $this->log->warning('登入成功，尚未完成信箱驗證');
                return ['msg' => '登入成功，尚未完成信箱驗證', 'type' => 'warning', 'redirect' => Config::getAppAddress().'auth/email/verified.php'];
            } 
            elseif ($user) {
                Session::set('USER_ID', $user->id);
                $this->log->info('登入成功');
                return ['msg' => '登入成功', 'type' => 'success', 'redirect' => Config::getAppAddress()];
            } 
            else {
                $this->log->error('登入失敗', ['account' => $data['email']]);
                return ['msg' => '登入失敗', 'type' => 'error', 'redirect' => Config::getAppAddress().'auth/login.php'];
            }
        }
        
        /**
         * 會員登出
         *
         * @return void
         */
        public function logout(): void
        {
            $this->log->info('登出成功');
            Session::remove('USER_ID');
            Message::flash('登出成功', 'success')->redirect(APP_ADDRESS.'auth/login.php');
        }

        /**
         * 會員註冊
         *
         * @param  array $request
         * @return array
         */
        public function register(array $request): array
        {
            $data = Security::defendFilter($request);
            
            $validation = $this->validation();

            $validData = $validation->run($data);

            if (!$validation->errors()) {
                $checkUser = Database::table('users')->where('email ="'.$validData['email'].'"')->first();
                // 密碼規則驗證
                if ($this->request->server->get('AUTH_PASSWORD_SECURITY') === 'TRUE') {
                    $safeCheck = Password::rule($validData['password']);
                }
                if ($checkUser) {
                    return ['msg' => '信箱已被註冊使用', 'type' => 'error'];
                }
                elseif ($this->request->server->get('AUTH_PASSWORD_SECURITY') === 'TRUE' && (count($safeCheck) <= 3 || !preg_match('/.{8,}/',$validData['password']))) {
                    return ['msg' => '密碼不符合規則，請參考密碼規則並再次確認', 'type' => 'error'];
                }
                elseif ($validData['password'] != $validData['password_confirm']) {
                    return ['msg' => '密碼要和確認密碼相同', 'type' => 'error'];
                }
                else {
                    unset($validData['password_confirm']);
                    $authCode = uniqid(mt_rand());
                    $validData['password'] = md5($validData['password']);
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
                    if ($this->request->server->get('AUTH_EMAIL_VERIFY') === 'TRUE') {
                        $name = $validData['name'];
                        $id = $validData['id'];
                        include_once('./email/content.php');
                        Mail::send($subject, $message, $validData['email'], $validData['name']);
                        return [
                            'msg' => '註冊成功，請前往註冊信箱收取認證信。',
                            'type' => 'success',
                            'redirect' => Config::getAppAddress().'auth/email/verified.php'
                        ];
                    }
                    else {
                        $this->log->info('註冊成功');
                        return ['msg' => '註冊成功。', 'type' => 'success', 'redirect' => Config::getAppAddress()];
                    }
                }
            } else {
                $this->errors = $validation->get_readable_errors();
                return ['msg' => '註冊失敗，請檢查輸入', 'type' => 'error'];
            }
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
    