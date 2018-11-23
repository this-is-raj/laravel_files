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
    Route::post('change_status/{name}', 'FilesController@changeStatus')->name('change_status');
});

$fileTypeRouteConfig = config('laravel_files.file_type_routes');

Route::group($fileTypeRouteConfig, function () {
    Route::get('students/file_type', 'FilesTypesController@fileTypes')->name('students.file_type');
    Route::post('students/file_type_update', 'FilesTypesController@fileTypesUpdate')->name('students.file_type_update');
});
