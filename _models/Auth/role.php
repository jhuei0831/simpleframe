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
            $data = Security::defendFilter($request);
            $gump = new GUMP();

            // 輸入驗證
            $gump->validation_rules([
                'name'    => 'required|max_len,30',
                'permission' => 'required'
            ]);

            // 輸入格式化
            $gump->filter_rules([
                'name'    => 'trim|sanitize_string',
            ]);

            $valid_data = $gump->run($data);

            $checkRole = Database::table('roles')->where("name = '".$valid_data['name']."'")->count();

            if ($checkRole > 0) {
                $error = true;
                Message::flash('名稱已存在。', 'error');
            }
            elseif ($gump->errors()) {
                $error = true;
                Message::flash('新增失敗，請檢查輸入。', 'error');
            }
            else {
                $insert = Database::table('roles')->insert(Toolbox::only($valid_data, ['token', 'name']), TRUE);
                foreach ($valid_data['permission'] as $value) {
                    Database::table('role_has_permissions')->CreateOrUpdate(['permission_id' => $value, 'role_id' => $insert], false);
                }  
                Message::flash('新增成功。', 'success')->redirect(APP_ADDRESS.'manage/roles');
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
            Database::table('roles')->where('id='.$id)->delete();
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
            $id = Security::defendFilter($_GET['id']);
            $data = Security::defendFilter($request);
            $gump = new GUMP();

            // 輸入驗證
            $gump->validation_rules([
                'name'    => 'required|max_len,30',
            ]);

            // 輸入格式化
            $gump->filter_rules([
                'name'    => 'trim|sanitize_string',
            ]);

            $valid_data = $gump->run($data);

            $checkRole = Database::table('roles')->where('name ="'.$valid_data['name'].'"')->count();

            if ($checkRole > 0 && $role->name != $valid_data['name']) {
                $error = true;
                Message::flash('名稱已存在。', 'error');
            }
            elseif ($gump->errors()) {
                $error = true;
                Message::flash('修改失敗，請檢查輸入。', 'error');
            }
            else {
                Database::table('roles')->where('id = '.$id)->update(Toolbox::only($valid_data, ['token', 'name']));
                Database::table('role_has_permissions')->where('role_id ='.$role->id)->delete();
                foreach ($valid_data['permission'] as $value) {
                    $newPermissions[] = ['permission_id' => $value, 'role_id' => $role->id];
                }  
                Database::table('role_has_permissions')->CreateOrUpdate($newPermissions, false);
                Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS.'manage/roles');
            }
        }
    }