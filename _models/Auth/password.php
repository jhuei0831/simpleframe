<?php

    namespace _models\Auth;

    use GUMP;
    use Kerwin\Core\Support\Facades\Config;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;
    use Kerwin\Core\Support\Facades\Session;

    class Password
    {        
        /**
         * 密碼重設資料驗證、更新
         *
         * @param  array $request
         * @return void
         */
        public function reset(array $request): void
        {
            $data = Security::defendFilter($request);
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

            $validData = $gump->run($data);

            $errors = [];
            // 密碼規則驗證
            if (PASSWORD_SECURE === 'TRUE') {
                $safeCheck = self::rule($_POST['password']);
            }
            $passwordResets = Database::table('password_resets')->where("id='{$_GET['id']}' and email_token='{$_GET['auth']}'")->first(false);
            $password = json_decode($passwordResets->password);
            if ($gump->errors()) {
                $errors[] = $gump->get_readable_errors();
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
                        'password_updated_at' => date('Y-m-d H:i:s'),
                    ], false);
                if ($updateUsers && $updatePasswordResets) {
                    Message::flash('密碼修改成功，請使用新密碼登入。', 'success')->redirect(APP_ADDRESS.'auth/login.php');
                }   
            }
        }
        
        /**
         * 密碼重設規範
         *
         * @param  string $root 路徑
         * @return void
         */
        public function resetVerify($root='../../'): void
        {
            // 禁止已登入或連結錯誤訪問
            if (!is_null(Session::get('USER_ID')) && empty($_GET['auth']) && empty($_GET['id'])) {
                include_once($root.'_error/404.php');
                exit;
            }

            // 確認連結資料正確性
            $passwordResets = Database::table('password_resets')->where("id='{$_GET['id']}' and email_token='{$_GET['auth']}'")->first();
            
            if (empty($passwordResets)) {
                Message::flash('連結有問題，請確認或重新申請密碼重設信件，謝謝', 'warning')->redirect(Config::getAppAddress().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') > strtotime($passwordResets->token_updated_at.' +30 minutes')) {
                Message::flash('密碼重設信已逾期，請重新獲取，謝謝。', 'warning')->redirect(Config::getAppAddress().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') < strtotime($passwordResets->password_updated_at.' +1 days')) {
                Message::flash('密碼更新時間小於一天，'.date('Y-m-d H:i:s', strtotime($passwordResets->password_updated_at.' +1 days')).'後才可以再次更改。', 'warning');
                Message::redirect(Config::getAppAddress());
            }
            else {
                return;
            }
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
    