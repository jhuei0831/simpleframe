<?php

    namespace _models;

    class Toolbox {
                
        /**
         * 移除陣列中不要的鍵值
         *
         * @param  array $array
         * @param  array|string $keys
         * @return void
         */
        public static function forget(&$array, $keys)
        {
            $keys = (array) $keys;

            if (count($keys) === 0) {
                return;
            }

            foreach ($keys as $key) {
                if (array_key_exists($key, $array)) {
                    unset($array[$key]);
                }
            }
        }
        
        /**
         * 執行forgot函式
         *
         * @param  array $array
         * @param  array|string $keys
         * @return array
         */
        public static function except($array, $keys)
        {
            static::forget($array, $keys);

            return $array;
        }
        
        /**
         * 只留下陣列中所需要的鍵值
         *
         * @param  array $array
         * @param  array|string $keys
         * @return array
         */
        public static function only($array, $keys)
        {
            $keys = (array) $keys;
            return array_intersect_key($array, array_flip($keys));
        }
    }