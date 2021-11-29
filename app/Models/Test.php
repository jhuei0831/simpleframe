<?php
    namespace models;

    class Test
    {
        public static function uuidToBinary(string $uuid)
        {
            $binary = pack("h*", str_replace('-', '', $uuid));
            return $binary;
        }

        public static function binaryToUuid(string $binary)
        {
            $uuid = unpack("h*", $binary);
            $uuid = preg_replace("/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/", "$1-$2-$3-$4-$5", $uuid);
            return $uuid[1];
        }
    }