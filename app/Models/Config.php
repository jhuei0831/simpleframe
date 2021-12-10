<?php

    namespace App\Models;

    use App\Models\Log\Log;
    use Kerwin\Core\Support\Toolbox;
    use Kerwin\Core\Support\Facades\Database;
    use Kerwin\Core\Support\Facades\Message;
    use Kerwin\Core\Support\Facades\Security;

    class Config 
    {        
        public $log;

        public function __construct() {
            $this->log = new Log('Config');
        }

        /**
         * 設定修改
         *
         * @param  array $request
         * @param  string $id
         * @return void
         */
        public function edit(array $request, string $id): void
        {
            $data = Security::defendFilter($request);
            if (array_key_exists('isOpen', $data)) {
                $data['isOpen'] = 1;
                $status = 'ON';
            }
            else {
                $data['isOpen'] = 0;
                $status = 'OFF';
            }

            $pattern = ['/^APP_STATUS=ON/m', '/^APP_STATUS=OFF/m'];
            $replacement = 'APP_STATUS='.$status;
            $subject = file_get_contents(APP_URL.'.env');

            // 修改.env
            file_put_contents(
                APP_URL.'.env', 
                preg_replace($pattern, $replacement, $subject)
            );

            Database::table('configs')->where("id = '{$id}'")->update($data);
            $this->log->info('修改設定', Toolbox::except($data, 'token'));
            Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/config.php');
        }
        
        /**
         * 設定內容
         *
         * @return object
         */
        public function index(): object
        {
            $config = Database::table('configs')->first();
            return $config;
        }
    }
    