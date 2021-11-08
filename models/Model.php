<?php

    namespace models;

    use Kerwin\Core\Support\Facades\Message;

    abstract class Model 
    {
        /**
         * 根據function返回的結果做處理
         *
         * @param  array $request
         * @return void
         */
        public function result(array $request): void
        {
            if (isset($request['redirect'])) {
                Message::flash($request['msg'], $request['type'])->redirect($request['redirect']);
            }
            else {
                Message::flash($request['msg'], $request['type']);
            }
        }
    }