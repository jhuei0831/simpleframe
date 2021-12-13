<?php

namespace App\Services;

use Exception;
use Kerwin\Core\Support\Facades\Database;

class  Datatable
{        
    /**
     * 資料庫欄位
     * 使用query的格式範例為:
     *  $columns = [
     *      0 => 'name',
     *      1 => 'email'
     *      2 => 'role',
     *      3 => 'created_at',
     *      4 => 'id'
     *  ]
     * 使用queryComplex的格式範例為:
     *  $columns = [
     *      'select' => [
     *          0 => ['column' => 'users.name', 'as' => 'name'],
     *          1 => ['column' => 'users.email', 'as' => 'email'],
     *          2 => ['column' => 'roles.name', 'as' => 'role'],
     *          3 => ['column' => 'users.created_at', 'as' => 'created_at'],
     *          4 => ['column' => 'users.id', 'as' => 'id']
     *      ],
     *      'join' => [
     *          ['column' => 2, 'table' => 'roles', 'on' => 'roles.id = users.role', 'condition' => 'roles.name']
     *      ]
     *  ]
     * @var array
     */
    protected $columns = [];
    
    /**
     * 行內搜尋條件
     *
     * @var array
     */
    protected $columnSearch = [];
        
    /**
     * 從資料庫取出的資料
     *
     * @var mixed
     */
    protected $data;
    
    /**
     * 資料表主鍵
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 送回datatable的資料
     *
     * @var array
     */
    protected $render = [];

    /**
     * datatable透過ajax送來的請求
     *
     * @var array
     */
    protected $request = [];

    /**
     * 資料表
     *
     * @var string
     */
    protected $table = '';
    
    /**
     * 資料數目(列)
     *
     * @var int
     */
    protected $totalRecords = 0;
    
    /**
     * 資料庫where條件
     *
     * @var string
     */
    protected $where = '1=1';

            
    /**
     * 如果不對資料做二次處理，直接render()即可
     *
     * @param  string $table 資料表
     * @param  array  $columns 資料欄位 
     * @param  array  $request datatable透過ajax送來的請求
     * @param  string $primaryKey 資料表主鍵
     * @return void
     */
    public function __construct(string $table, array $columns, array $request, string $primaryKey = 'id')
    {
        $this->columns = $columns;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->request = $request;
    }
    
    /**
     * 建立搜尋條件
     *
     * @return string
     */
    public function filter(): string
    {
        if (isset($this->request['columns']) && $this->request['draw'] != 1) {
            for ( $i=0; $i<count($this->request['columns']); $i++) {
                $requestColumn = $this->request['columns'][$i];
                $str = $requestColumn['search']['value'];
                $data = is_array($requestColumn['data']) ? $requestColumn['data']['_'] : $requestColumn['data'];
                if ($requestColumn['searchable'] == 'true' && $str != '' && !is_array($requestColumn['data'])) {
                    $this->columnSearch[] = $data." LIKE '%".$str."%'";
                }
            }
            $this->where = implode(' AND ', $this->columnSearch);
        }
        return $this->where;
    }
    
    /**
     * 建立搜尋條件:包含join及替換join後的資料欄位名稱
     *
     * @return void
     */
    public function queryComplex()
    {
        $select = [];
        $originColumn = [];
        $replaceColumn = [];
        
        foreach ($this->columns['select'] as $column) {
            if (!is_array($column)) {
                throw new Exception("column must be Array", 1);
            }
            elseif (empty($column['column']) || empty($column['as'])) {
                throw new Exception("Column array must has 'column' and 'as' index", 1);
            }
            $originColumn[] = '/'.$column['as'].'/';
            $replaceColumn[] = $column['column'];
            $select[] = $column['column'].' as '.$column['as'];
        }

        /* 替換join後的資料欄位名稱 */
        $select = implode(', ', $select);
        $where = preg_replace($originColumn, $replaceColumn, $this->filter());

        $this->data = Database::table($this->table)->select($select);
        
        /* 因為要把代碼轉中文並搜尋，所以join其他資料表 */
        if (empty($this->columns['join'])) {
            throw new Exception("queryComplex columns must has join", 1);
        }

        foreach ($this->columns['join'] as $join) {
            if (empty($join['column']) || empty($join['table']) || empty($join['on']) || empty($join['condition'])) {
                throw new Exception("Column array must has 'column', 'table', 'on', 'condition' index", 1);
            }
            $condition = ($this->request['columns'][$join['column']]['search']['value'] == '') ? 
            '' : 
            "and ".$join['condition']." like '%".$this->request['columns'][$join['column']]['search']['value']."%'";
            $this->data = $this->data->join($join['table'], $join['on'].' '.$condition);
        }

        $this->data = $this->data
            ->where($where)
            ->orderby([[$this->columns['select'][$this->request['order'][0]['column']]['column'], $this->request['order'][0]['dir']]])
            ->limit($this->request['start'], $this->request['length'])
            ->get();
        
        return $this->data;
    }
    
    /**
     * 取得資料庫資料
     *
     * @return array
     */
    public function query(): array
    {
        $where = $this->filter();
        $this->data = Database::table($this->table)
            ->select(implode(", ", $this->columns))
            ->where($where)
            ->orderby([[$this->columns[$this->request['order'][0]['column']], $this->request['order'][0]['dir']]])
            ->limit($this->request['start'], $this->request['length'])
            ->get();
        
        return $this->data;
    }
    
    /**
     * 送回datatable的資料
     *
     * @return array
     */
    public function render($complex = false): array
    {
        $data =  ($complex == false) ? $this->query() : $this->queryComplex();
        $draw = $this->request['draw'];
        $totalRecords = $this->totalRecords();
        $recordsFiltered = $this->resultFilterLength();
        $this->render = [
            "draw"            => intval($draw),   
            "recordsTotal"    => intval($totalRecords),  
            "recordsFiltered" => intval($recordsFiltered->count),
            "data"            => $data,
        ];

        return $this->render;
    }
    
    /**
     * 取得過濾後的資料總數
     *
     * @return object
     */
    public function resultFilterLength(): object
    {
        $where = $this->filter();
        $this->data = Database::table($this->table)
            ->select("COUNT(`{$this->primaryKey}`) AS count")
            ->where($where)
            ->first();
        
        return $this->data;
    }
    
    /**
     * 取得資料總數
     *
     * @return void
     */
    public function totalRecords(): int
    {
        $this->totalRecords = Database::table($this->table)->count();
        return $this->totalRecords;
    }
}
    