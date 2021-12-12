<?php

namespace App\Http\Controller\Manage;

use App\Models\Datatable;
use Kerwin\Core\Request;
use Twig\Environment;

class LogController
{
        
    /**
     * log管理頁面
     *
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function index(Environment $twig)
    {
        echo $twig->render('manage/logs/index.twig');
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
            0 => 'id',
            1 => 'ip',
            2 => 'level',
            3 => 'message',
            4 => 'created_at',
            5 => 'browser',
            6 => 'user',
            7 => 'channel',
            8 => 'context',
            9 => 'platform',
        );
    
        $datatable = new Datatable('logs', $columns, $request->request->all());
    
        $data = $datatable->render();
        
        echo json_encode($data);
    }
}
