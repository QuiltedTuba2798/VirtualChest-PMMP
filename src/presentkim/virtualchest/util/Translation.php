<?php

namespace presentkim\virtualchest\util;

class Translation{

    /** @var string[string] */
    private static $lang = [];

    /** @var string[string] */
    private static $default = [];

    /**
     * @param string $filename
     * @param bool   $default
     */
    public static function load(string $filename, boolean $default = false){
        if ($default) {
            self::$default = yaml_parse_file($filename);
        } else {
            self::$lang = yaml_parse_file($filename);
        }
    }

    /** @param resource $resource */
    public static function loadFromResource($resource){
        if (is_resource($resource)) {
            self::$lang = yaml_parse(stream_get_contents($resource));
        }
    }

    /**
     * @param string $filename
     *
     * @return bool Returns TRUE on
     *              success.
     */
    public static function save(string $filename){
        $path = dirname($filename);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return yaml_emit_file($filename, self::$lang, YAML_UTF8_ENCODING);
    }

    /**
     * @param string   $strId
     * @param string[] $params
     *
     * @return string
     */
    public static function translate(string $strId, string ...$params){
        if (isset(self::$lang[$strId])) {
            $value = self::$lang[$strId];
            if (is_array($value)) {
                $value = $value[array_rand($value)];
            }
            if (is_string($value)) {
                return empty($params) ? $value : strtr($value, listToPairs($params));
            } else {
                return "$strId is not string";
            }
        }
        return "Undefined \$strId : $strId";
    }

    /**
     * @param string $strId
     *
     * @return string[] | null
     */
    public static function getArray(string $strId){
        if (isset(self::$lang[$strId])) {
            $value = self::$lang[$strId];
            return is_array($value) ? $value : null;
        }
        return null;
    }
}