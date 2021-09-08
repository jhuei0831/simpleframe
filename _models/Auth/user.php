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

    class User 
    {                  
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
                $profile_error = false;
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

                $valid_data = $gump->run($data);

                if (!$gump->errors()) {
                    $update = Database::table('users')->where("id = '{$id}'")->update($valid_data);
                    Message::flash('修改成功，謝謝。', 'success');
                    Message::redirect(APP_ADDRESS . 'manage/users');
                } else {
                    $profile_error = true;
                    Message::flash('修改失敗，請檢查輸入。', 'error');
                    // Message::redirect(APP_ADDRESS.'manage/users/edit.php?id='.$id);
                }
            } else {
                $password_error = false;
                unset($data['type']);
                $gump = new GUMP();

                // 輸入驗證
                $gump->validation_rules([
                    'password'    => 'required|max_len,30|min_len,8',
                    'password_confirm'    => 'required|max_len,30|min_len,8',
                ]);

                // 輸入格式化
                $gump->filter_rules([
                    'password'    => 'trim',
                    'password_confirm'   => 'trim',
                ]);
                $valid_data = $gump->run($data);

                if ($data['password'] != $data['password_confirm']) {
                    $password_error = true;
                    Message::flash('密碼要和確認密碼相同!。', 'error');
                } elseif ($gump->errors()) {
                    $password_error = true;
                    Message::flash('修改失敗，請檢查輸入。', 'error');
                    // Message::redirect(APP_ADDRESS.'manage/users/edit.php?id='.$id); 
                } else {
                    unset($valid_data['password_confirm']);
                    $valid_data['password'] = md5($valid_data['password']);
                    $update = Database::table('users')->where("id = '{$id}'")->update($valid_data);
                    Message::flash('修改成功，謝謝。', 'success');
                    Message::redirect(APP_ADDRESS . 'manage/users');
                }
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
            Database::table('users')->where('id='.$id)->delete();
            Message::flash('刪除成功，謝謝。', 'success');
            Message::redirect(APP_ADDRESS.'manage/users');
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
            if ($data['checkword'] != $_SESSION['check_word']) {
                Message::flash('驗證碼錯誤', 'error');
                Message::redirect(APP_ADDRESS . 'auth/login.php');
            } 
            elseif ($user && empty($user->email_varified_at) && EMAIL_VERIFY === 'TRUE') {
                $_SESSION['USER_ID'] = $user->id;
                Message::flash('登入成功，尚未完成信箱驗證', 'warning');
                Message::redirect(APP_ADDRESS . 'auth/email/verified.php');
            } 
            elseif ($user) {
                $_SESSION['USER_ID'] = $user->id;
                Message::flash('登入成功', 'success');
                Message::redirect(APP_ADDRESS);
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
            unset($_SESSION['USER_ID']);

            Message::flash('登出成功', 'success');
            Message::redirect(APP_ADDRESS.'auth/login.php');
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
                Database::table('password_resets')->CreateOrUpdate([
                    'token' => $data['token'], 
                    'id' => $id, 
                    'password' => isset($passwordResets->password) ? $passwordResets->password : json_encode([$user->password]),
                    'email_token' => $authCode, 
                    'token_updated_at' => date('Y-m-d H:i:s'), 
                    'password_updated_at' => isset($passwordResets->password_updated_at) ? $passwordResets->password_updated_at : $user->created_at, 
                ]);
                Message::flash('請前往註冊信箱收取密碼重設信，謝謝。', 'success');
                Message::redirect(APP_ADDRESS.'auth/password/password_forgot.php');
            }
            else{
                Message::flash('獲取信件失敗', 'error');
                Message::redirect(APP_ADDRESS.'auth/password/password_forgot.php');
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

            $valid_data = $gump->run($data);

            $check_user = Database::table('users')->where('email ="'.$valid_data['email'].'"')->first();
            $errors = [];
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
            elseif ($gump->errors()) {
                $errors[] = $gump->get_readable_errors();
                Message::flash('註冊失敗，請檢查輸入', 'error');
            }
            else {
                unset($valid_data['password_confirm']);
                $authCode = uniqid(mt_rand());
                $valid_data['password'] = md5($valid_data['password']);
                $valid_data['id'] = Toolbox::UUIDv4();
                $valid_data['role'] = 2;
                $valid_data['auth_code'] = $authCode;
                $insert = Database::table('users')->insert($valid_data, TRUE);
                // 取得剛剛註冊的帳號ID
                $insertId = Database::table('users')->where("email = '{$valid_data['email']}'")->first();
                $id = $insertId->id;
                $_SESSION['USER_ID'] = $id;
                if (EMAIL_VERIFY==='TRUE') {
                    $name = $valid_data['name'];
                    include_once('./email/content.php');
                    Mail::send($subject, $message, $valid_data['email'], $valid_data['name']);
                    Message::flash('註冊成功，請前往註冊信箱收取認證信。', 'success');
                    Message::redirect(APP_ADDRESS.'auth/email/verified.php');
                }
                else {
                    Message::flash('註冊成功。', 'success');
                    Message::redirect(APP_ADDRESS);
                }
            }
        }
    }
    