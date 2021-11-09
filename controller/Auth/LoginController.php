<?php

    namespace Controller\Auth;

    use Twig\Environment;
    use models\Log\Log;
    use Kerwin\Captcha\Captcha;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Config;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Session;
    use Kerwin\Core\Support\Facades\Security;
    use Symfony\Component\HttpFoundation\Request;

    class LoginController
    {

        public function captcha()
        {
            //設置定義為圖片
            header("Content-type: image/PNG");

            $captcha = new Captcha();
            $captcha->getImageCode(1,5,130,30);
        }

        public function index(Environment $twig)
        {
            echo $twig->render('auth/login.twig');
        }

        /**
         * login
         *
         * @param array $request
         * @return array
         */
        public function login(Request $request, Log $log): array
        {
            $post = Toolbox::only($request->request->all(), ['token', 'email', 'password', 'captcha']);
            $data = Security::defendFilter($post);
            $user = Database::table('users')->where('email ="'.$data['email'].'" and password ="'.md5($data['password']).'"')->first();
            if ($data['captcha'] != Session::get('captcha')) {
                return ['msg' => '驗證碼錯誤', 'type' => 'error', 'redirect' => Config::getAppAddress().'auth/login.php'];
            } 
            elseif ($user && empty($user->email_varified_at) && EMAIL_VERIFY === 'TRUE') {
                Session::set('USER_ID', $user->id);
                // $this->log->warning('登入成功，尚未完成信箱驗證');
                return ['msg' => '登入成功，尚未完成信箱驗證', 'type' => 'warning', 'redirect' => Config::getAppAddress().'auth/email/verified.php'];
            } 
            elseif ($user) {
                Session::set('USER_ID', $user->id);
                $log->info('登入成功');
                Message::flash('登入成功', 'success')->redirect(Config::getAppAddress());
                // return ['msg' => '登入成功', 'type' => 'success', 'redirect' => Config::getAppAddress()];
            } 
            else {
                // $this->log->error('登入失敗', ['account' => $data['email']]);
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
            // $this->log->info('登出成功');
            Session::remove('USER_ID');
            Message::flash('登出成功', 'success')->redirect(APP_ADDRESS.'auth/login');
        }
    }
    