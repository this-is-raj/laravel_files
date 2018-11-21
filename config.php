<?php
/**
 * Created by Raj Kumar.
 * Date: 10/15/18
 * Time: 11:54 AM
 */

return [
    'owners' => [
        //
    ],

    'file_routes' => [
        'namespace' => 'Raj\LaravelFiles\Http\Controllers',

        'middleware' => ['web','auth'],

        'as' => 'files.',

        'prefix' => 'files/'
    ],

    'file_type_routes' => [
        'namespace' => 'Raj\LaravelFiles\Http\Controllers',

        'middleware' => ['web', 'auth'],

        'as' => 'files_types.',

        'prefix' => 'file_type/'
    ],

];
