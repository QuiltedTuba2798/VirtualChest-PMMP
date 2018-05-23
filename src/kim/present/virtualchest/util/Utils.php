<?php

declare(strict_types=1);

namespace kim\present\virtualchest\util;

class Utils{

    /**
     * @param string $str
     * @param array  $strs
     *
     * @return bool
     */
    public static function in_arrayi(string $str, array $strs) : bool{
        foreach ($strs as $key => $value) {
            if (strcasecmp($str, $value) === 0) {
                return true;
            }
        }
        return false;
    }
}