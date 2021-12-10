<?php

namespace App\Http\Controller\Manage;

use App\Models\Datatable;
use Kerwin\Core\Request;
use Twig\Environment;

class PermissionsController
{
    
    public function index(Environment $twig)
    {
        echo $twig->render('manage/permissions/index.twig');
    }

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
}
