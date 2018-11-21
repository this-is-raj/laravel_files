<?php
/**
 * Created by PhpStorm.
 * User: froiden
 * Date: 10/12/18
 * Time: 5:42 PM
 */

namespace Raj\LaravelFiles\Model;

use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    protected $table = 'file_types';

    const ALLOWED_FILE_TYPE = [
        'student'
    ];

    private static $owners = null;

    public static function refreshOwners () {
        self::setOwners();
    }

    public static function getOwners() {
        if (!empty(self::$owners)) {
            return self::$owners;
        }

        self::refreshOwners();

        return self::$owners;
    }

    public static function getFileTypesByOwner(string $owner, bool $onlyRequired = false) {

        if (!empty(self::$owners)) {
            return self::$owners[$owner];
        }

        self::refreshOwners();

        return self::$owners[$owner];
    }

    public function getFileTypes($onlyRequired = false) {
        if (isset($this->owner) && $this->owner) {
            $owner = $this->owner;
        } elseif (isset($this->owner) && $this->owner) {
            $owner = $this->find($this->id)->first()->owner;
        } else {
            return False;
        }
        return self::getFileTypesByOwner($owner, $onlyRequired);
    }

    private static function setOwners() {
        $allFileTypes = self::all();
        $owners = [];
        foreach ($allFileTypes as $file) {
            $ownerType = $file->owner;
            $fileType = $file->fileTypes;
            $subFileType = $file->subFileTypes;

            $dataArray = [
                'id' => $file->id,
                'maxFileCounts' => $file->maxFileCounts,
                'hasCrop' => $file->hasCrop,
                'hasResize' => $file->hasResize,
                'mimes' => $file->mimes,
                'isRequired' => $file->isRequired,
                'needApproval' => $file->needApproval,
                'extraConstraints' => $file->extraConstraints
            ];

            if (array_key_exists($ownerType, $owners)) {
                $ownerFileType = &$owners[$ownerType]['fileTypes'];
                if (array_key_exists($fileType, $ownerFileType)) {
                    $ownerSubFileType = &$ownerFileType[$fileType]['subFileType'];
                    $ownerSubFileType[$subFileType] = $dataArray;
                } else {
                    $ownerFileType[$fileType] = [
                        'subFileType' => [
                            $subFileType => $dataArray
                        ]
                    ];
                }
            } else {
                $owners[$ownerType] = [
                    'className' => $file->className,
                    'fileTypes' => [
                        $fileType => [
                            'subFileType' => [
                                $subFileType => $dataArray
                            ]
                        ]
                    ]
                ];
            }
        }

        self::$owners = $owners;
    }

    public function getExtraConstraintsAttribute($value) {
        return (array) json_decode($value);
    }

    public function setExtraConstraintsAttribute($value) {
        $this->attributes['extraConstraints'] = json_encode($value);
    }

}
