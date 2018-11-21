<?php

use Illuminate\Database\Seeder;
use Raj\LaravelFiles\Model\Files;
use Raj\LaravelFiles\Model\FileType;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Empty Existing file_types.
        DB::statement("SET foreign_key_checks=0");
        FileType::truncate();
        DB::statement("SET foreign_key_checks=1");

        // Populate file_types table

        FileType::insert([
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'student_photo',
                'subFileTypes' => '',
                'maxFileCounts' => '1',
                'hasCrop' => true,
                'hasResize' => true,
                'mimes' => 'jpeg,png,bmp',
                'isRequired' => true,
                'needApproval' => false,
                'isMutable' => false,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'school_records',
                'subFileTypes' => 'birth_certificate',
                'maxFileCounts' => '1',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => true,
                'needApproval' => true,
                'isMutable' => false,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'school_records',
                'subFileTypes' => 'academic_records',
                'maxFileCounts' => '',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => true,
                'needApproval' => true,
                'isMutable' => true,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'medical_records',
                'subFileTypes' => 'bgg',
                'maxFileCounts' => '',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => true,
                'needApproval' => true,
                'isMutable' => false,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'medical_records',
                'subFileTypes' => 'pcv',
                'maxFileCounts' => '',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => true,
                'needApproval' => true,
                'isMutable' => true,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'medical_records',
                'subFileTypes' => 'men_c',
                'maxFileCounts' => '',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => true,
                'needApproval' => true,
                'isMutable' => true,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'immunisation_records',
                'subFileTypes' => '',
                'maxFileCounts' => '',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => true,
                'needApproval' => true,
                'isMutable' => false,
            ],
            [
                'owner' => 'student',
                'className' => Files::class,
                'fileTypes' => 'other_documents',
                'subFileTypes' => '',
                'maxFileCounts' => '',
                'hasCrop' => false,
                'hasResize' => false,
                'mimes' => '',
                'isRequired' => false,
                'needApproval' => false,
                'isMutable' => true,
            ],
        ]);
    }
}
