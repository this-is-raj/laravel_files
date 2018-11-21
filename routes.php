<?php
/**
 * User: Raj Kumar
 * Date: 10/15/18
 * Time: 11:49 AM
 */

$fileRouteConfig = config('laravel_files.file_routes');

Route::group($fileRouteConfig, function () {
    Route::post('upload', ['as' => 'upload', 'uses' => 'FilesController@upload']);
    Route::get('download/{name}', ['as' => 'download', 'uses' => 'FilesController@download']);
    Route::delete('delete/{name}', ['as' => 'delete', 'uses' => 'FilesController@delete']);
});

$fileTypeRouteConfig = config('laravel_files.file_type_routes');

Route::group($fileTypeRouteConfig, function () {
    //
});
