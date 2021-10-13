<?php

    namespace _models\Auth;

    use GUMP;
    use _models\Log\Log;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;

    class Permission
    {             
        public $log;

        public function __construct() {
            $this->log = new Log('Permission');
        }  

        /**
         * 權限新增
         *
         * @param array $request
         * @return void
         */
        public function create(array $request): void
        {
            global $errors;

            $data = Security::defendFilter($request);
            
            $gump = $this->validation();

            $valid_data = $gump->run($data);

            if (!$gump->errors()) {
                $permission = Database::table('permissions')->where("name = '{$valid_data['name']}'")->first();
                if ($permission) {
                    $errors[] = '名稱'.$valid_data['name'].'已存在';
                    Message::flash('新增失敗，請檢查輸入', 'error');
                }
                else {
                    Database::table('permissions')->insert($valid_data);
                    $this->log->info('新增權限', Toolbox::except($valid_data, 'token'));
                    Message::flash('新增成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/permissions');
                }
            } 
            else {
                $errors = $gump->get_readable_errors();
                Message::flash('新增失敗，請檢查輸入。', 'error');
            }
        }
        
        /**
         * 權限刪除
         *
         * @param int $id
         * @return void
         */
        public function delete(int $id): void
        {
            $check = Database::table('role_has_permissions')->where("permission_id ='{$id}'")->count();
            if ($check > 0) {
                Message::flash('尚有角色使用此權限', 'warning')->redirect(APP_ADDRESS . 'manage/permissions');
            }
            else {
                $permission = Database::table('permissions')->find($id);
            }
            Database::table('permissions')->where("id = '{$id}'")->delete();
            $this->log->info('刪除權限', ['name' => $permission->name]);
            Message::flash('刪除成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/permissions');
        }

        /**
         * 權限修改
         *
         * @param  array $request
         * @param    int $id
         * @return void
         */
        public function edit(array $request, int $id): void
        {
            global $errors;

            $data = Security::defendFilter($request);

            $gump = $this->validation();

            $valid_data = $gump->run($data);

            if (!$gump->errors()) {
                $permission = Database::table('permissions')->where("name = '{$valid_data['name']}'")->first();
                if ($permission && $permission->id != $id) {
                    $errors[] = '名稱'.$valid_data['name'].'已存在';
                    Message::flash('修改失敗，請檢查輸入。', 'error');
                }
                else {
                    Database::table('permissions')->where("id = '{$id}'")->update($valid_data);
                    $this->log->info('修改權限', Toolbox::except($valid_data, 'token'));
                    Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/permissions');
                }
            } else {
                $errors = $gump->get_readable_errors();
                Message::flash('修改失敗，請檢查輸入。', 'error');
            }
        }

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