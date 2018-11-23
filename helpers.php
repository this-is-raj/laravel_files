<?php
/**
 * Created by Raj Kumar.
 * Date: 11/21/18
 * Time: 5:44 PM
 */

if (!function_exists('asset_url')) {

    /**
     * @param $path
     * @return string
     */
    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        $storageUrl = \Storage::url($path);
        if (!Str::startsWith($storageUrl, 'http')) {
            return config('app.url').$storageUrl;
        }

        return $storageUrl;
    }

}

if (!function_exists('string_camel_case')) {

    /**
     * @param $string
     * @return string
     */
    // @codingStandardsIgnoreLine
    function string_camel_case($string) {
        return ucwords(str_replace( ['_', '-'], ' ', $string ));
    }

}

if (!function_exists('string_snake_case')) {

    /**
     * @param $string
     * @return string
     */
    // @codingStandardsIgnoreLine
    function string_snake_case($string) {
        return strtolower(str_replace( ['-', ' '], '_', $string ));
    }

}
