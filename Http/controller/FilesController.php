<?php

namespace Raj\LaravelFiles\Http\Controllers;

use Raj\LaravelFiles\Model\Files;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileUploadRequest;

class FilesController extends Controller
{
    public function __construct() {
        //
    }

    /**
     * All the input related help for file uploading can be found in FileUploadRequest Class.
     * @param FileUploadRequest $request
     * @return array
     */
    public function upload(FileUploadRequest $request) {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->file;
        $folder = $request->type;
        $documentType = $request->file_type ?: '';
        $documentSubType = $request->sub_file_type ?: '';

        if ($request->has('type') && $request->has('id')
            && array_key_exists($request->type, self::ownerTypes)) {
            $ownerType = self::ownerTypes[$request->type];
            $owner = (new $ownerType['className'])->with('files')->find($request->get('id'));

            //Check for multiple files
            if (count($owner->files) &&
                array_key_exists($documentType, $ownerType['fileTypes']) &&
                isset($ownerType['fileTypes'][$documentType]['maxFileCounts']) &&
                $ownerType['fileTypes'][$documentType]['maxFileCounts'] == 1
            ) {
                foreach ($owner->files as $file) {
                    if ($file->file_type == $documentType) {
                        $this->deleteFileByName($file->given_name);
                    }
                }
            }
        } else {
            $owner = null;
        }

        // Get image crop data
        if ($request->has('crop') && $request->crop == 'true') {
            $crop = [
                'width' => $request->width,
                'height' => $request->height,
                'x' => $request->x,
                'y' => $request->y,
                'rotate' => $request->rotate,
            ];
        }
        else {
            $crop = null;
        }

        if (!$uploadedFile || $uploadedFile && !$uploadedFile->isValid()) {
            return response(['status' => 'fail', 'message' => 'File was not uploaded.'], 422);
        }

        $newName = self::generateNewFileName($uploadedFile->getClientOriginalName());

        $tempPath = storage_path('app/temp/' . $newName);

        /** Check if folder exits or not. If not then create the folder */
        if(!\File::exists(base_path('public/storage/' . $folder))) {
            $result = \File::makeDirectory(base_path('public/storage/' . $folder), 0775, true);
        }

        /** @var UploadedFile $uploadedFile */
        $uploadedFile->move(storage_path('app/temp'), $newName);

        if (!empty($crop)) {
            // Crop image
            $image = \Image::make($tempPath);

            if ($crop['rotate'] !== '') {
                $image->rotate(-1 * $crop['rotate']);
            }

            $image->crop(round($crop['width']), round($crop['height']), round($crop['x']), round($crop['y']));

            if ($folder === 'profile_images') {
                // Resize profile images to standard size
                $image->resize(500, 500);
            }
            else if ($folder === 'photos') {
                $image->resize(1500, 1500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $image->save();
        }

        $size = \File::size($tempPath);
        $mime = \File::mimeType($tempPath);

        \Storage::putFileAs('public/' . $folder, new \Illuminate\Http\File($tempPath), $newName, 'public');

        \File::delete($tempPath);

        $fileObj = new Files();
        $fileObj->original_name = $uploadedFile->getClientOriginalName();
        $fileObj->given_name = $newName;
        $fileObj->mime_type = $mime;
        $fileObj->file_type = $documentType;
        $fileObj->sub_file_type = $documentSubType;

        if ($owner) {
            $fileObj->owner_type = $ownerType['className'];
            $fileObj->owner_id = $owner->id;
        }

        $fileObj->size = $size;
        $fileObj->folder = 'public/'. $folder;
        $fileObj->uploaded_from_ip = $request->getClientIp();
        $fileObj->save();

        return Reply::successWithData('File Uploaded Successfully', [
            'name' => $newName,
            'url' => asset_url($folder . '/' . $newName),
            'download_url' => route('admin.files.download', $newName)
        ]);
    }

    /**
     * Public wrapper for file delete
     *
     * @param $name
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete($name) {
        return $this->deleteFileByName(($name)) ?
            response(['status' => 'success', 'message' => 'File Deleted Successfully.']) :
            response(['status' => 'fail', 'message' => 'File could not be deleted'], 403);

    }

    /**
     * If the uploaded file need approval this function changes it approval status.
     *
     * @param string $name
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function changeStatus(string $name) {
        $file = Files::where('given_name', $name)->first();

        if (! $file) {
            return response(['status' => 'fail', 'message' => 'Invalid file name.'], 400);
        }

        try {
            $file->approved = ! $file->approved;
            $file->save();
            return response(['status' => 'success', 'message' => 'File status changed Successfully.']);
        } catch (\Exception $e) {
            return response(['status' => 'fail', 'message' => 'File status could not be changed'], 403);
        }
    }

    /**
     * Download the file with given filename using Stream
     *
     * @param $name
     * @return mixed
     */
    public function download($name)
    {
        $file = Files::where('given_name', $name)->first();

        $fs = \Storage::disk(config('filesystem.default'))->getDriver();
        $stream = $fs->readStream($file->folder . '/' . $file->given_name);

        return \Response::stream(function() use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $file->type,
            'Content-Length' => $file->size,
            'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"',
        ]);
    }

    /**
     * Delete the file with given name if it exists.
     *
     * @param string $name
     * @return bool
     */
    private function deleteFileByName(string $name) {
        $file = Files::where('given_name', $name)->first();
        if (!$file) {
            return false;
        }

        try {
            $filePath = storage_path('app/' . $file->folder . '/' . $file->given_name);
            \File::delete($filePath);
            $file->delete();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Generate a new file name with the current file extension.
     *
     * @param $currentFileName
     * @return string
     */
    private static function generateNewFileName($currentFileName)
    {
        $ext = strtolower(\File::extension($currentFileName));

        $newName = md5(microtime());

        if ($ext === '') {
            return $newName;
        }
        else {
            return $newName . '.' . $ext;
        }
    }

}
