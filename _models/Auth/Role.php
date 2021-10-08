<?php

    namespace _models\Auth;

    use GUMP;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;

    class Role 
    {        
        /**
         * 角色新增
         *
         * @param  array $request
         * @return void
         */
        public function create(array $request): void
        {
            global $errors;

            $data = Security::defendFilter($request);
            
            $gump = $this->validation();

            $valid_data = $gump->run($data);

            if (!$gump->errors()) {
                $checkRole = Database::table('roles')->where("name = '".$valid_data['name']."'")->count();

                if ($checkRole > 0) {
                    Message::flash('名稱已存在。', 'error')->redirect(APP_ADDRESS.'manage/roles/create.php');
                }
                Database::table('roles')->insert(Toolbox::only($valid_data, ['token', 'name']), TRUE);
                $role = Database::table('roles')->where("name = '".$valid_data['name']."'")->first();
                foreach ($valid_data['permission'] as $value) {
                    Database::table('role_has_permissions')
                        ->createOrUpdate([
                            'permission_id' => $value,
                            'role_id' => $role->id
                        ], false);
                }  
                Message::flash('新增成功。', 'success')->redirect(APP_ADDRESS.'manage/roles');
            }
            else {
                $errors = $gump->get_readable_errors();
                Message::flash('新增失敗，請檢查輸入。', 'error');
            }
        }
        
        /**
         * 角色刪除
         *
         * @param  string $id
         * @return void
         */
        public function delete(string $id): void
        {
            $check = Database::table('users')->where('role ='.$id)->count();
            if ($check > 0) {
                Message::flash('此角色尚有使用者使用', 'warning')->redirect(APP_ADDRESS.'manage/roles');
            }
            Database::table('roles')->where("id='{$id}'")->delete();
            Message::flash('刪除成功，謝謝。', 'success')->redirect(APP_ADDRESS.'manage/roles');
        }
        
        /**
         * 角色修改
         *
         * @param  array $request
         * @return void
         */
        public function edit(array $request, object $role): void
        {
            global $errors;

            $id = Security::defendFilter($_GET['id']);
            $data = Security::defendFilter($request);
            
            $gump = $this->validation();

            $valid_data = $gump->run($data);

            if (!$gump->errors()) {
                $checkRole = Database::table('roles')->where('name ="'.$valid_data['name'].'"')->count();

                if ($checkRole > 0 && $role->name != $valid_data['name']) {
                    Message::flash('名稱已存在。', 'error')->redirect(APP_ADDRESS.'manage/roles/edit.php?id='.$id);
                }

                Database::table('roles')->where('id = '.$id)->update(Toolbox::only($valid_data, ['token', 'name']));
                Database::table('role_has_permissions')->where('role_id ='.$role->id)->delete();
                if (isset($valid_data['permission'])) {
                    foreach ($valid_data['permission'] as $value) {
                        $newPermissions[] = ['permission_id' => $value, 'role_id' => $role->id];
                    }  
                    Database::table('role_has_permissions')->createOrUpdate($newPermissions, false);
                }
                Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS.'manage/roles');
            }
            
            else {
                $errors = $gump->get_readable_errors();
                Message::flash('修改失敗，請檢查輸入。', 'error');
            }
        }

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