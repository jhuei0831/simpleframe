<?php

namespace App\Http\Controller\Manage;

use Twig\Environment;
use App\Models\Log\Log;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Toolbox;
use Kerwin\Core\Support\Facades\Message;
use Kerwin\Core\Support\Facades\Database;
use Kerwin\Core\Support\Facades\Security;

class ConfigController
{
        
    /**
     * 網站管理頁面
     *
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function index(Environment $twig)
    {
        $configs = Database::table('configs')->get();
        echo $twig->render('manage/config.twig', [
            'configs' => $configs
        ]);
    }
    
    /**
     * 網站管理修改
     *
     * @param  \Kerwin\Core\Request $request
     * @param  \App\Models\Log\Log $log
     * @param  string $id
     * @return void
     */
    public function edit(Request $request, Log $log, string $id)
    {
        $data = Security::defendFilter($request->request->all());
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
        $log->info('修改設定', Toolbox::except($data, 'token'));
        Message::flash('修改成功，謝謝。', 'success')->redirect(APP_ADDRESS . 'manage/config');
    }
}
