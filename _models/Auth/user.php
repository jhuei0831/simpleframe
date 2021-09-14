<?php

    namespace _models\Auth;

    use GUMP;
    use _models\Auth\Password;
    use Kerwin\Core\Mail;
    use Kerwin\Core\Support\Config;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Session;

    class User 
    {                          
        /**
         * 新增使用者
         *
         * @param array $request
         * @return void
         */
        public function create(array $request): void
        {
            global $errors;

            $data = Security::defendFilter($request);
            $gump = new GUMP();

            // 輸入驗證
            $gump->validation_rules([
                'name'              => 'required|max_len,30',
                'email'             => 'required|valid_email',
                'role'              => 'required',
                'password'          => 'required|max_len,30|min_len,8',
                'password_confirm'  => 'required|max_len,30|min_len,8',
            ]);

            // 輸入格式化
            $gump->filter_rules([
                'name'              => 'trim|sanitize_string',
                'email'             => 'trim|sanitize_email',
                'password'          => 'trim',
                'password_confirm'  => 'trim',
            ]);

            // 錯誤訊息
            $gump->set_fields_error_messages([
                'name'              => ['required' => '名稱必填', 'max_len' => '名稱必須小於或等於30個字元'],
                'email'             => ['required' => '電子郵件必填', 'valid_email' => '必須符合電子郵件格式'],
                'role'              => ['required' => '角色必填'],
                'password'          => ['required' => '密碼必填', 'max_len' => '密碼必須小於等於30個字元', 'min_len' => '密碼必須大於等於8個字元'],
                'password_confirm'  => ['required' => '確認密碼必填', 'max_len' => '確認密碼必須小於等於30個字元', 'min_len' => '確認密碼必須大於等於8個字元'],
            ]);

            $valid_data = $gump->run($data);

            if (!$gump->errors()) {
                $check_user = Database::table('users')->where('email ="'.$valid_data['email'].'"')->first();
                // 密碼規則驗證
                if (PASSWORD_SECURE === 'TRUE') {
                    $safeCheck = Password::rule($_POST['password']);
                }
                if ($check_user) {
                    Message::flash('信箱已被註冊使用', 'error');
                }
                elseif (PASSWORD_SECURE === 'TRUE' && (count($safeCheck) <= 3 || !preg_match('/.{8,}/',$valid_data['password']))) {
                    Message::flash('密碼不符合規則，請參考密碼規則並再次確認', 'error');
                }
                elseif ($valid_data['password'] != $valid_data['password_confirm']) {
                    Message::flash('密碼要和確認密碼相同', 'error');
                }
                else {
                    unset($valid_data['password_confirm']);
                    $authCode = uniqid(mt_rand());
                    $valid_data['password'] = md5($valid_data['password']);
                    $valid_data['id'] = Toolbox::UUIDv4();
                    $valid_data['auth_code'] = $authCode;
                    Database::table('users')->insert($valid_data, TRUE);
                    Message::flash('新增成功。', 'success')->redirect(APP_ADDRESS.'manage/users');
                }
            } else {
                $errors = $gump->get_readable_errors();
                Message::flash('新增失敗，請檢查輸入', 'error');
            }
        }

        /**
         * 使用者刪除
         *
         * @param  string $id
         * @return void
         */
        public function delete(string $id): void
        {
            Database::table('users')->where("id='{$id}'")->delete();
            Message::flash('刪除成功，謝謝。', 'success')->redirect(APP_ADDRESS.'manage/users');
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
            global $errors;

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
                    'name'              => ['required' => '名稱必填', 'max_len' => '名稱必須小於或等於30個字元'],
                    'email'             => ['required' => '電子郵件必填', 'valid_email' => '必須符合電子郵件格式'],
                    'role'              => ['required' => '角色必填'],
                ]);

                $valid_data = $gump->run($data);

                if (!$gump->errors()) {
                    Database::table('users')->where("id = '{$id}'")->update($valid_data);
                    Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
                } else {
                    $errors = $gump->get_readable_errors();
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
                    'password'          => ['required' => '密碼必填', 'max_len' => '密碼必須小於等於30個字元', 'min_len' => '密碼必須大於等於8個字元'],
                    'password_confirm'  => ['required' => '確認密碼必填', 'max_len' => '確認密碼必須小於等於30個字元', 'min_len' => '確認密碼必須大於等於8個字元'],
                ]);

                // 輸入格式化
                $gump->filter_rules([
                    'password'    => 'trim',
                    'password_confirm'   => 'trim',
                ]);
                $valid_data = $gump->run($data);

                if ($data['password'] != $data['password_confirm']) {
                    Message::flash('密碼要和確認密碼相同!。', 'error');
                } elseif ($gump->errors()) {
                    $errors = $gump->get_readable_errors();
                    Message::flash('修改失敗，請檢查輸入。', 'error');
                } else {
                    unset($valid_data['password_confirm']);
                    $valid_data['password'] = md5($valid_data['password']);
                    Database::table('users')->where("id = '{$id}'")->update($valid_data);
                    Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
                }
            }
        }     

        /**
         * login
         *
         * @param array $request
         * @return void
         */
        public function login(array $request): void
        {
            $data = Security::defendFilter($request);
            $user = Database::table('users')->where('email ="' . $data['email'] . '" and password ="' . md5($data['password']) . '"')->first();
            if ($data['captcha'] != Session::get('captcha')) {
                Message::flash('驗證碼錯誤'.Session::get('captcha'), 'error')->redirect(APP_ADDRESS . 'auth/login.php');
            } 
            elseif ($user && empty($user->email_varified_at) && EMAIL_VERIFY === 'TRUE') {
                Session::set('USER_ID', $user->id);
                Message::flash('登入成功，尚未完成信箱驗證', 'warning')->redirect(APP_ADDRESS . 'auth/email/verified.php');
            } 
            elseif ($user) {
                Session::set('USER_ID', $user->id);
                Message::flash('登入成功', 'success')->redirect(APP_ADDRESS);
            } 
            else {
                Message::flash('登入失敗', 'error');
            }
        }
        
        /**
         * 會員登出
         *
         * @return void
         */
        public function logout(): void
        {
            Session::remove('USER_ID');
            Message::flash('登出成功', 'success')->redirect(APP_ADDRESS.'auth/login.php');
        }
        
        /**
         * 忘記密碼
         *
         * @param array $request
         * @return void
         */
        public function passwordForgot(array $request): void
        {
            $data = Security::defendFilter($request);
            $authCode = Security::defendFilter(uniqid(mt_rand()));
            $user = Database::table('users')->where("email = '{$data['email']}'")->first();
            $passwordResets = Database::table('password_resets')->where("id = '{$user->id}'")->first(false);
            // 確認密碼上次更新時間
            if (strtotime('now') < strtotime($passwordResets->password_updated_at.' +1 days')) {
                Message::flash('密碼更新時間小於一天，'.date('Y-m-d H:i:s', strtotime($passwordResets->password_updated_at.' +1 days')).'後才可以再次更改。', 'warning');
                Message::redirect(Config::getAppAddress());
            }
            // 放到信中的變數
            $name = $user->name;
            $id = $user->id;
            include_once('./content.php');
            $mail = Mail::send($subject, $message, $user->email, $user->name);
            if ($mail) {
                Database::table('password_resets')->createOrUpdate([
                    'token' => $data['token'], 
                    'id' => $id, 
                    'password' => isset($passwordResets->password) ? $passwordResets->password : json_encode([$user->password]),
                    'email_token' => $authCode, 
                    'token_updated_at' => date('Y-m-d H:i:s'), 
                    'password_updated_at' => isset($passwordResets->password_updated_at) ? $passwordResets->password_updated_at : $user->created_at, 
                ]);
                Message::flash('請前往註冊信箱收取密碼重設信，謝謝。', 'success')->redirect(APP_ADDRESS.'auth/password/password_forgot.php');
            }
            else{
                Message::flash('獲取信件失敗', 'error')->redirect(APP_ADDRESS.'auth/password/password_forgot.php');
            }
        }

        /**
         * 會員註冊
         *
         * @param  array $request
         * @return void
         */
        public function register(array $request): void
        {
            global $errors;
            $data = Security::defendFilter($request);
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

            $valid_data = $gump->run($data);

            if (!$gump->errors()) {
                $check_user = Database::table('users')->where('email ="'.$valid_data['email'].'"')->first();
                // 密碼規則驗證
                if (PASSWORD_SECURE === 'TRUE') {
                    $safeCheck = Password::rule($_POST['password']);
                }
                if ($check_user) {
                    Message::flash('信箱已被註冊使用', 'error');
                }
                elseif (PASSWORD_SECURE === 'TRUE' && (count($safeCheck) <= 3 || !preg_match('/.{8,}/',$valid_data['password']))) {
                    Message::flash('密碼不符合規則，請參考密碼規則並再次確認', 'error');
                }
                elseif ($valid_data['password'] != $valid_data['password_confirm']) {
                    Message::flash('密碼要和確認密碼相同', 'error');
                }
                else {
                    unset($valid_data['password_confirm']);
                    $authCode = uniqid(mt_rand());
                    $valid_data['password'] = md5($valid_data['password']);
                    $valid_data['id'] = Toolbox::UUIDv4();
                    $valid_data['role'] = 2;
                    $valid_data['auth_code'] = $authCode;
                    Database::table('users')->insert($valid_data);
                    Session::set('USER_ID', $valid_data['id']);
                    if (EMAIL_VERIFY==='TRUE') {
                        $name = $valid_data['name'];
                        include_once('./email/content.php');
                        Mail::send($subject, $message, $valid_data['email'], $valid_data['name']);
                        Message::flash('註冊成功，請前往註冊信箱收取認證信。', 'success')->redirect(APP_ADDRESS.'auth/email/verified.php');
                    }
                    else {
                        Message::flash('註冊成功。', 'success')->redirect(APP_ADDRESS);
                    }
                }
            } else {
                $errors = $gump->get_readable_errors();
                Message::flash('註冊失敗，請檢查輸入', 'error');
            }
        }
    }
    