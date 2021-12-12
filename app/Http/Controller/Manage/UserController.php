<?php

namespace App\Http\Controller\Manage;

use GUMP;
use App\Models\Datatable;
use App\Models\Log\Log;
use App\Models\Auth\Password;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Config;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Security;
use Twig\Environment;

class UserController
{

    /**
     * twig
     *
     * @var \Twig\Environment
     */
    private $twig;
    
    /**
     * 角色清單
     *
     * @var array
     */
    private $roles;

    public function __construct(Environment $twig) {
        $this->roles = Database::table('roles')->get();
        $this->twig = $twig;
    }

    /**
     * 使用者管理頁面
     *
     * @return void
     */
    public function index()
    {
        echo $this->twig->render('manage/users/index.twig', [
            'roles' => $this->roles
        ]);
    }
    
    /**
     * 使用者新增頁面
     *
     * @return void
     */
    public function create()
    {
        echo $this->twig->render('manage/users/create.twig', [
            'roles' => $this->roles
        ]);
    }
 
    /**
     * 使用者新增
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Models\Log\Log $log
     * @return void
     */
    public function store(Request $request, Log $log)
    {
        $post = Toolbox::only($request->request->all(), ['token', 'name', 'email', 'role', 'password', 'password_confirm']);
        $data = Security::defendFilter($post);
            
        $validation = $this->validation();

        $validData = $validation->run($data);

        if (!$validation->errors()) {
            $checkUser = Database::table('users')->where('email ="'.$validData['email'].'"')->first();
            // 密碼規則驗證
            if ($request->server->get('AUTH_PASSWORD_SECURITY') === 'TRUE') {
                $safeCheck = Password::rule($validData['password']);
            }
            if ($checkUser) {
                Message::flash('信箱已被註冊使用', 'error');
            }
            elseif ($request->server->get('AUTH_PASSWORD_SECURITY') === 'TRUE' && (count($safeCheck) <= 3 || !preg_match('/.{8,}/',$validData['password']))) {
                Message::flash('密碼不符合規則，請參考密碼規則並再次確認', 'error');
            }
            elseif ($validData['password'] != $validData['password_confirm']) {
                Message::flash('密碼要和確認密碼相同', 'error');
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
                $log->info('新增使用者', ['id' => $validData['id']]);
                Message::flash('新增成功。', 'success')->redirect(Config::getAppAddress().'manage/users');
            }
            echo $this->twig->render('manage/users/create.twig', [
                'post' => Toolbox::only($data, ['name', 'email', 'role']),
                'roles' => $this->roles
            ]);
        } else {
            $errors = $validation->get_readable_errors();
            Message::flash('新增失敗，請檢查輸入', 'error');
            echo $this->twig->render('manage/users/create.twig', [
                'errors' => $errors,
                'post' => Toolbox::only($data, ['name', 'email', 'role']),
                'roles' => $this->roles
            ]);
        }
    }

    /**
     * 使用者修改頁面
     *
     * @param string $id
     * @return void
     */
    public function edit(string $id)
    {
        $user = Database::table('users')->find($id);
        echo $this->twig->render('manage/users/edit.twig', [
            'roles' => $this->roles,
            'user' => $user
        ]);
    }
       
    /**
     * 使用者修改
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Models\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function update(Request $request, Log $log, string $id)
    {
        $post = Toolbox::only($request->request->all(), ['token', 'type', 'name', 'email', 'role', 'password', 'password_confirm']);
        $data = Security::defendFilter($post);
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
                $log->info('修改使用者資料', ['id' => $id, 'data' => Toolbox::except($validData, 'token')]);
                Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
            } else {
                $errors = $gump->get_readable_errors();
                Message::flash('修改失敗，請檢查輸入。', 'error');
                echo $this->twig->render('manage/users/create.twig', [
                    'errors' => $errors,
                    'post' => Toolbox::only($data, ['name', 'email', 'role']),
                    'roles' => $this->roles
                ]);
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
                $errors = $gump->get_readable_errors();
                Message::flash('修改失敗，請檢查輸入。', 'error');
                echo $this->twig->render('manage/users/create.twig', [
                    'errors' => $errors,
                    'post' => Toolbox::only($data, ['name', 'email', 'role']),
                    'roles' => $this->roles
                ]);
            } else {
                unset($validData['password_confirm']);
                $validData['password'] = md5($validData['password']);
                Database::table('users')->where("id = '{$id}'")->update($validData);
                $log->info('修改使用者密碼', ['id' => $id]);
                Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
            }
        }
    }
    
    /**
     * 使用者刪除
     *
     * @param  \App\Models\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function delete(Log $log, string $id)
    {
        Database::table('users')->where("id='{$id}'")->delete();
        $log->info('刪除使用者', ['id' => $id]);
        Message::flash('刪除成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/users');
    }

    /**
     * 要呈現在Datatable上的資料
     *
     * @param  \Kerwin\Core\Request $request
     * @return void
     */
    public function dataTable(Request $request)
    {
        $columns = array( 
            0 => 'name',
            1 => 'email',
            2 => 'role',
            3 => 'created_at',
            4 => 'id',
        );

        $datatable = new Datatable('users', $columns, $request->request->all());

        $data = $datatable->render();

        echo json_encode($data);
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
