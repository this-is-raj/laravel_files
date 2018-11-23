<?php
/**
 * Created by PhpStorm.
 * User: froiden
 * Date: 10/16/18
 * Time: 11:37 AM
 */

namespace App\Http\Requests\File;

use App\FileType;
use App\Http\Requests\Request;

class FileTypeUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'fileType' => 'required,in:'. implode(',', FileType::ALLOWED_FILE_TYPE)
        ];
    }
}
