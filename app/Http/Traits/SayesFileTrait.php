<?php

namespace App\Http\Traits;

use App\Models\SayesFile;

trait SayesFileTrait
{
    use FileUploaderTrait;
    /**
     * @param $imageArray
     * @param $sayesId
     * @param $status
     * @return bool
     */
    public function uploadSayesFiles($imageArray, $sayesId , $status): bool
    {
        foreach ($imageArray as $key => $image) {
            if ($key === 'civil_front') {
                $type = 5;
            } elseif ($key === 'civil_back') {
                $type = 6;
            } elseif ($key === 'criminal') {
                $type = 7;
            } else {
                return false;
            }
            if (is_file($image)) {
                $imageName = $this->UploadImage('SayesFile', $image);
                $file = SayesFile::create([
                    'file_type_id' => $type,
                    'user_id' => $sayesId,
                    'image' => $imageName,
                    'status' => $status
                ]);
                if (!$file)
                    return false;
            }
            else{
                return false;
            }
        }
        return true;
    }

}
