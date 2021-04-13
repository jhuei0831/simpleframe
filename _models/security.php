<?php
    namespace _models;

    use Exception;

    class Security
    {        
        /**
         * check_csrf 防止跨站請求偽造 (Cross-site request forgery)
         *
         * @param  array $data
         * @return boolean
         */
        public static function check_csrf($data)
        {
            if (empty($data['token'])) {
                if (IS_DEBUG === 'TRUE') {
                    throw new Exception('請進行CSRF驗證');
                }
                return false;
            }
            elseif($data['token'] != TOKEN)
            {
                if (IS_DEBUG === 'TRUE') {
                    throw new Exception('禁止跨域請求');
                }
                return false;
            }
            else{
                return true;
            }    
        }
                
        /**
         * defend_filter 用 addslashes防SQL Injection、filter_var防XSS
         *
         * @param  array|string $data
         * @return array|string
         */
        public static function defend_filter($data)
        {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        self::defend_filter($value);
                    }
                    else {
                        $value  = addslashes($value);
                    }
                }
                $data[$key] = $value;
                return filter_var_array($data, FILTER_SANITIZE_STRING);
            }
            else{
                $data  = addslashes($data);
                return filter_var($data, FILTER_SANITIZE_STRING);
            }
        }
    }
    