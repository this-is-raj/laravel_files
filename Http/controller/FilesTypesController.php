<?php

namespace Raj\LaravelFiles\Http\Controllers;

use App\Http\Requests\File\FileTypeUpdateRequest;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use Raj\LaravelFiles\Model\FileType;

class FilesTypesController extends Controller
{
    /**
     * This property let you know  about  all the ownerType  and their  supported file properties.
     *
     * className => full qualified name of the owner's  Model class, Required for morph relations.
     *
     * ***Important Note: Don't forget to include App/Http/Traits/HasFile Trait in Model class with this className.
     *
     *
     * fileType => An array of all supported files with keys as fileType name and value is another
     *      Array containing properties of that file type.
     *      For Example: Each student has avatar and one student can have only one avatar hence we
     *      can set maxFileCounts properties = 1
     *      Note: in future we can define and set more properties
     *      Such as: 1. We can validate allowed mimeTypes of file directly from here.
     *               2. Allow and disallow crop and resize on uploaded file.
     *               3. Custom storage location for uploaded file.
     *               4. Is document necessary and must be uploaded for the given ownerType.
     *               5. Document validity period (In this facility we can implement that document automatically
     *                  get deleted after validity expires or we can send notification to upload new document.)
     *
    */
    const ownerTypes = [
        'student' => [
            'className' => Model::class,
            'fileTypes' => [
                'student_photo' => ['maxFileCounts' => 1],
                'birth_certificate' => ['maxFileCounts' => 1],
                'medical_records' => [],
                'immunisation_records' => [],
                'other_documents' => []
            ],
        ],
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fileTypes() {
        $this->pageTitle = 'Student File Types';
        $this->availableType = FileType::getFileTypesByOwner('student', false)['fileTypes'];
        return view('admin.students.file_types', $this->data);
    }

    /**
     * @param FileTypeUpdateRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function fileTypesUpdate(FileTypeUpdateRequest $request) {
        $type = string_snake_case($request->fileType);

        $requestData = ['id', 'subFileTypes', 'maxFileCounts', 'isRequired', 'hasCrop', 'hasResize', 'needApproval'];

        if($type == 'medical_records') {
            $requestData = array_merge($requestData, array('minAge', 'maxAge'));
        }

        $requestData = $request->only($requestData);

        $storeData = [];
        foreach($requestData as $name => $rData) {
            foreach($rData as $key => $rD) {
                if (! array_key_exists($key, $storeData)) {
                    $storeData[$key] = [];
                }
                $storeData[$key][$name] = $rD;
            }
        }

        try {
            // If any id is not provided that means file_type with corresponding id has been deleted.
            $requestedIds = $request->only('id');
            FileType::where('owner', 'student')->where('fileTypes', $type)->where('isMutable', 1)->whereNotIn('id', $requestedIds ? $requestedIds['id'] : [])->delete();

            foreach($storeData as $data) {
                $extraConstraints = $type == 'medical_records' ? array('minAge' => $data['minAge'], 'maxAge' => $data['maxAge']) : array();
                if ($this->getData('id', $data)) {
                    $fileType = FileType::find($data['id']);
                    if ($fileType->isMutable) $fileType->fileTypes = $type;
                } else {
                    $fileType = new FileType();
                    $fileType->fileTypes = $type;
                }

                $fileType->owner = 'student';
                $fileType->className = Student::class;
                if ($d = $this->getData ('subFileTypes', $data)) $fileType->subFileTypes = string_snake_case($data['subFileTypes']);
                if ($d = $this->getData ('maxFileCounts', $data)) $fileType->maxFileCounts = $data['maxFileCounts'];
                if ($d = $this->getData ('isRequired', $data)) $fileType->isRequired = $data['isRequired'];
                if ($d = $this->getData ('hasCrop', $data)) $fileType->hasCrop = $data['hasCrop'];
                if ($d = $this->getData ('hasResize', $data)) $fileType->hasResize = $data['hasResize'];
                if ($d = $this->getData ('needApproval', $data)) $fileType->needApproval = $data['needApproval'];
                $fileType->extraConstraints = $extraConstraints;
                $fileType->save();
            }

            FileType::refreshOwners();
            return response(['message' => 'Document types updated successfully.']);
        } catch (\Exception $e) {
            return response(['status' => 'fail', 'message' => 'Some unknown error occur while updating document types.'], 500);
        }
    }

    /**
     * Return value at the give key if it exits in the given array else return null.
     *
     * @param $key
     * @param $array
     * @return null
     */
    private function getData ($key, array $array) {
        if (array_key_exists($key, $array)) return $array[$key];
        return null;
    }
}
