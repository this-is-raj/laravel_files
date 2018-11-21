<?php
/**
 * Created by PhpStorm.
 * User: froiden
 * Date: 10/8/18
 * Time: 11:44 AM
 */

namespace App\Http\Traits;

use App\Files;
use App\FileType;

trait HasFile {

    /**
     * Morph relation fetch all files whose owner is student.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files () {
        return $this->morphMany(Files::class, 'owner');
    }

    /**
     *
     * Return an array of all documents that have not been provided.
     *
     * @param string $ownerType
     * We can explicitly provide the name of the owner for which we want to get the data, but it
     * is still fine in case if we don't provide this value. In this case it will fetch the name
     * with the help of Model Class Name but it is a little bit costlier because of extra datab'
     * se query.
     *
     * @param bool $onlyRequired
     * If the param $onlyRequired is set to false all the missing file are returned other wise it
     * return only required files.
     *
     * @return mixed
     */
    public function getMissingFiles (string $ownerType = null, bool $onlyRequired = true) {
        if (!$ownerType) {
            if(isset($this->owner)) {
                $ownerType = $this->owner;
            } else {
                $ownerType = FileType::where('className', self::class)->first()->owner;
            }
        }

        $missingFiles = FileType::getFileTypesByOwner($ownerType)['fileTypes'];

        $providedFiles = !empty($this->files) ? $this->files : $this->files()->get();

        foreach ($providedFiles as $file) {
            if (array_key_exists($file->file_type, $missingFiles)) {
                $subFileType = $file->sub_file_type ?: '';
                if (array_key_exists($subFileType, $missingFiles[$file->file_type]['subFileType'])) {
                    unset($missingFiles[$file->file_type]['subFileType'][$subFileType]);
                }
                if (empty($missingFiles[$file->file_type]['subFileType'])) {
                    unset($missingFiles[$file->file_type]);
                }
            }
        }


        foreach($missingFiles as $key1 => $fileType) {
            foreach ($fileType['subFileType'] as $key2 => $subType) {
                if (!$this->applyExtraConstraints($key1, $key2, $subType)) {
                    unset($fileType['subFileType'][$key2]);
                }
            }
            if(empty($fileType['subFileType'])) {
                unset($missingFiles[$key1]);
            }
        }

        if (!$onlyRequired) {
            return $missingFiles;
        }

        foreach($missingFiles as $key1 => $fileType) {
            foreach ($fileType['subFileType'] as $key2 => $subType) {
                if (!$subType['isRequired']) {
                    unset($fileType['subFileType'][$key2]);
                }
            }
            if(empty($fileType['subFileType'])) {
                unset($missingFiles[$key1]);
            }
        }

        return $missingFiles;
    }

    /**
     *
     * This function is intended for the purpose to use/override in case when we are using
     * extra constraints in out model.
     *
     * We can implement validation logic for extra constraints by overriding this function
     * in Model.
     *
     * @return bool
     *
     */
    public function applyExtraConstraints($fileType, $subFileType, $data) {
        return true;
    }
}
