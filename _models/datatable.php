<?php

    namespace _models;

    use Kerwin\Core\Support\Facades\Database;

    class  Datatable
    {        
        /**
         * 資料庫欄位
         *
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
         * @param  array $columns 資料欄位 
         * @param  array $request datatable透過ajax送來的請求
         * @return void
         */
        public function __construct(string $table, array $columns, array $request)
        {
            $this->columns = $columns;
            $this->table = $table;
            $this->request = $request;
        }
        
        /**
         * 建立搜尋條件
         *
         * @return string
         */
        private function filter(): string
        {
            if (isset($this->request['columns']) && $this->request['draw'] != 1) {
                for ( $i=0; $i<count($this->request['columns']); $i++) {
                    $requestColumn = $this->request['columns'][$i];
                    $str = $requestColumn['search']['value'];
        
                    if ($requestColumn['searchable'] == 'true' && $str != '') {
                        $this->columnSearch[] = $requestColumn['data']." LIKE '%".$str."%'";
                    }
                }
                $this->where = implode(' AND ', $this->columnSearch);
            }
            
            return $this->where;
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
        public function render(): array
        {
            $data = $this->query();
            $draw = $this->request['draw'];
            $totalRecords = $this->totalRecords();
            $recordsFiltered = count($data);
            $this->render = [
                "draw"            => intval($draw),   
                "recordsTotal"    => intval($totalRecords),  
                "recordsFiltered" => intval($recordsFiltered),
                "data"            => $data,
            ];

            return $this->render;
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
    