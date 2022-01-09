<?php

namespace App\Http\Controller\Manage;

use GUMP;
use App\Services\Datatable;
use App\Services\Log\Log;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Security;
use Twig\Environment;

class RoleController
{
    
    /**
     * twig
     *
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }
    
    /**
     * 角色管理頁面
     *
     * @return void
     */
    public function index()
    {
        echo $this->twig->render('manage/roles/index.twig');
    }
    
    /**
     * 角色新增頁面
     *
     * @return void
     */
    public function create()
    {
        $permissions = Database::table('permissions')->get();
        echo $this->twig->render('manage/roles/create.twig', [
            'permissions' => $permissions
        ]);
    }
    
    /**
     * 角色新增
     *
     * @param  Kerwin\Core\Request $request
     * @param  App\Services\Log\Log $log
     * @return void
     */
    public function store(Request $request, Log $log)
    {
        $permissions = Database::table('permissions')->get();
        $post = Toolbox::only($request->request->all(), ['token', 'name', 'permission']);
        $data = Security::defendFilter($post);
            
        $validation = $this->validation();

        $validData = $validation->run($data);

        if (!$validation->errors()) {
            $checkRole = Database::table('roles')->where("name = '".$validData['name']."'")->count();

            if ($checkRole > 0) {
                Message::flash('名稱已存在。', 'error');
                echo $this->twig->render('manage/roles/create.twig', [
                    'errors' => ['名稱'.$validData['name'].'已存在'],
                    'post' => Toolbox::only($data, ['name', 'permission']),
                    'permissions' => $permissions
                ]);
            }
            Database::table('roles')->insert(Toolbox::only($validData, ['token', 'name']), TRUE);
            $role = Database::table('roles')->where("name = '".$validData['name']."'")->first();
            foreach ($validData['permission'] as $value) {
                Database::table('role_has_permissions')
                    ->createOrUpdate([
                        'permission_id' => $value,
                        'role_id' => $role->id
                    ], false);
            }  
            $log->info('新增角色', Toolbox::except($validData, 'token'));
            Message::flash('新增成功。', 'success')->redirect(APP_ADDRESS.'manage/roles/');
        }
        else {
            $this->errors = $validation->get_readable_errors();
            Message::flash('新增失敗，請檢查輸入。', 'error');
            echo $this->twig->render('manage/roles/create.twig', [
                'errors' => $this->errors,
                'post' => Toolbox::only($data, ['name', 'permission'])
            ]);
        }
    }
    
    /**
     * 角色編輯頁面
     *
     * @param  string $id
     * @return void
     */
    public function edit(string $id)
    {
        $permissions = Database::table('permissions')->get();
        $role = Database::table('roles')->find($id);
        $roleHasPermissions = array_column(Database::table('role_has_permissions')->where('role_id = '.$role->id)->get(), 'permission_id');
        echo $this->twig->render('manage/roles/edit.twig', [
            'permissions' => $permissions,
            'role' => $role,
            'roleHasPermissions' => $roleHasPermissions
        ]);
    }
    
    /**
     * 角色編輯
     *
     * @param  Kerwin\Core\Request $request
     * @param  App\Services\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function update(Request $request, Log $log, string $id)
    {
        $permissions = Database::table('permissions')->get();
        $role = Database::table('roles')->find($id);
        $roleHasPermissions = array_column(Database::table('role_has_permissions')->where('role_id = '.$role->id)->get(), 'permission_id');
        $post = Toolbox::only($request->request->all(), ['token', 'name', 'permission']);
        $data = Security::defendFilter($post);
        
        $validation = $this->validation();

        $validData = $validation->run($data);

        if (!$validation->errors()) {
            $checkRole = Database::table('roles')->where('name ="'.$validData['name'].'"')->count();

            if ($checkRole > 0 && $role->name != $validData['name']) {
                Message::flash('名稱已存在。', 'error');
                echo $this->twig->render('manage/roles/edit.twig', [
                    'errors' => ['名稱'.$validData['name'].'已存在'],
                    'post' => Toolbox::only($data, ['name', 'permission']),
                    'roleHasPermissions' => $roleHasPermissions,
                    'permissions' => $permissions
                ]);
            }

            Database::table('roles')->where('id = '.$id)->update(Toolbox::only($validData, ['token', 'name']));
            Database::table('role_has_permissions')->where('role_id ='.$role->id)->delete();
            if (isset($validData['permission'])) {
                foreach ($validData['permission'] as $value) {
                    $newPermissions[] = ['permission_id' => $value, 'role_id' => $role->id];
                }  
                Database::table('role_has_permissions')->createOrUpdate($newPermissions, false);
            }
            $log->info('修改角色', Toolbox::except($validData, 'token'));
            Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS.'manage/roles/');
        }
        
        else {
            $this->errors = $validation->get_readable_errors();
            Message::flash('修改失敗，請檢查輸入。', 'error');
            echo $this->twig->render('manage/roles/edit.twig', [
                'errors' => $this->errors,
                'post' => Toolbox::only($data, ['name', 'permission']),
                'roleHasPermissions' => $roleHasPermissions,
                'permissions' => $permissions
            ]);
        }
    }
    
    /**
     * delete
     *
     * @param  App\Services\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function delete(Log $log, string $id)
    {
        $id = Security::defendFilter($id);
        $check = Database::table('users')->where('role ='.$id)->count();
        if ($check > 0) {
            Message::flash('此角色尚有使用者使用', 'warning')->redirect(APP_ADDRESS.'manage/roles/');
        }
        else {
            $role = Database::table('roles')->find($id);
        }
        
        Database::table('roles')->where("id='{$id}'")->delete();
        $log->info('刪除角色', ['name' => $role->name]);
        Message::flash('刪除成功，謝謝。', 'success')->redirect(APP_ADDRESS.'manage/roles/');
    }

    /**
     * 要呈現在Datatable上的資料
     *
     * @param  Kerwin\Core\Request $request
     * @return void
     */
    public function dataTable(Request $request)
    {
        $columns = array( 
            0 => 'name',
            1 => 'created_at',
            2 => 'id',
        );

        $datatable = new Datatable('roles', $columns, $request->request->all());

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
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'name'    => 'trim|sanitize_string',
        ]);

        // 錯誤訊息
        $gump->set_fields_error_messages([
            'name'    => ['required' => '名稱必填', 'max_len' => '名稱必須小於或等於30個字元'],
        ]);

        return $gump;
    }
}
