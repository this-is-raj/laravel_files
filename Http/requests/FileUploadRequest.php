<?php

namespace App\Http\Requests\File;

use App\Http\Controllers\ManageFilesController;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class FileUploadRequest extends Request
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
        Validator::extend('typeValidation', function ($attributes, $values) {
            if ($attributes == 'type') {
                return array_key_exists($values, ManageFilesController::ownerTypes);
            }
            return false;
        });

        // Allowed file types.
        $rules = [
            'file' => 'mimes:csv,jpeg,png,bmp,doc,docx,pdf,xls,xlsx,ppt,pptx,txt'
        ];

        // If request has POLYMORPHIC RELATION information.
        // When ever we add a new polymorphic relation we must enter its type and id.
        // Where:
        //     type => full qualified name of morphed class. and
        //     id => value of primary key attribute in morphed class.
        // For creating any polymorphic relation we must pass file_for input and define
        // this type in ManageFilesController::$ownerTypes property.
        // And include App/Http/Traits/HasFile trait in your corresponding Model class.
        if ($this->has('type') && $this->has('id')) {
            $rules = array_merge([
                'type' => 'string|typeValidation',
            ], $rules);
        }

        // If polymorphic class contain more than one type of file than we can pass its
        // type in file_type input field.
        $rules = array_merge([
            'file_type' => 'string|nullable'
        ], $rules);


        return $rules;
    }
}
