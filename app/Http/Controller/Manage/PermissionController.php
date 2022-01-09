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

class PermissionController
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
     * 權限管理頁面
     *
     * @return void
     */
    public function index()
    {
        echo $this->twig->render('manage/permissions/index.twig');
    }
    
    /**
     * 權限新增頁面
     *
     * @return void
     */
    public function create()
    {
        echo $this->twig->render('manage/permissions/create.twig');
    }

    /**
     * 權限新增
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Services\Log\Log $request
     * @return void
     */
    public function store(Request $request, Log $log)
    {
        $post = Toolbox::only($request->request->all(), ['token', 'name', 'description']);
        $data = Security::defendFilter($post);
        $validation = $this->validation();

        $validData = $validation->run($data);

        if (!$validation->errors()) {
            $permission = Database::table('permissions')->where("name = '{$validData['name']}'")->first();
            if ($permission) {
                Message::flash('新增失敗，請檢查輸入', 'error');
                echo $this->twig->render('manage/permissions/create.twig', [
                    'errors' => ['名稱'.$validData['name'].'已存在'],
                    'post' => Toolbox::only($data, ['name', 'description'])
                ]);
            }
            else {
                Database::table('permissions')->insert($validData);
                $log->info('新增權限', Toolbox::except($validData, 'token'));
                Message::flash('新增成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/permissions/');
            }
        } 
        else {
            $this->errors = $validation->get_readable_errors();
            Message::flash('新增失敗，請檢查輸入。', 'error');
            echo $this->twig->render('manage/permissions/create.twig', [
                'errors' => $this->errors,
                'post' => Toolbox::only($data, ['name', 'description'])
            ]);
        }
    }
    
    /**
     * 權限編輯頁面
     *
     * @param  string $id
     * @return void
     */
    public function edit(string $id)
    {
        $permission = Database::table('permissions')->find($id);
        echo $this->twig->render('manage/permissions/edit.twig', [
            'permission' => $permission
        ]);
    }

    /**
     * 權限更新
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Services\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function update(Request $request, Log $log, string $id)
    {
        $post = Toolbox::only($request->request->all(), ['token', 'name', 'description']);
        $data = Security::defendFilter($post);

        $validation = $this->validation();

        $validData = $validation->run($data);

        if (!$validation->errors()) {
            $permission = Database::table('permissions')->where("name = '{$validData['name']}'")->first();
            if ($permission && $permission->id != $id) {
                $errors[] = '名稱'.$validData['name'].'已存在';
                Message::flash('修改失敗，請檢查輸入。', 'error');
            }
            else {
                Database::table('permissions')->where("id = '{$id}'")->update($validData);
                $log->info('修改權限', Toolbox::except($validData, 'token'));
                Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/permissions/');
            }
        } else {
            $this->errors = $validation->get_readable_errors();
            Message::flash('修改失敗，請檢查輸入。', 'error');
            echo $this->twig->render('manage/permissions/create.twig', [
                'errors' => $this->errors,
                'post' => Toolbox::only($data, ['name', 'description'])
            ]);
        }
    }
    
    /**
     * 權限刪除
     *
     * @param  \App\Services\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function delete(Log $log, string $id)
    {
        $check = Database::table('role_has_permissions')->where("permission_id = '{$id}'")->count();
        if ($check > 0) {
            Message::flash('尚有角色使用此權限', 'warning')->redirect(APP_ADDRESS . 'manage/permissions/');
        }
        else {
            $permission = Database::table('permissions')->find($id);
        }
        Database::table('permissions')->where("id = '{$id}'")->delete();
        $log->info('刪除權限', ['name' => $permission->name]);
        Message::flash('刪除成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/permissions/');
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
            1 => 'description',
            2 => 'created_at',
            3 => 'id',
        );
    
        $datatable = new Datatable('permissions', $columns, $request->request->all());
    
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
            'name'          => 'required|max_len,20',
            'description'   => 'required|max_len,30',
        ]);

        // 輸入格式化
        $gump->filter_rules([
            'name'        => 'trim|sanitize_string',
            'description' => 'trim|sanitize_string',
        ]);

        // 錯誤訊息
        $gump->set_fields_error_messages([
            'name'         => ['required' => '名稱必填', 'max_len' => '名稱必須小於或等於20個字元'],
            'description'  => ['required' => '敘述必填', 'max_len' => '敘述必須小於或等於30個字元'],
        ]);

        return $gump;
    }
}
