<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * Trait FileUploaderTrait
 */
trait FileUploaderTrait
{
    /**
     * function Cloud server S3 storage files
     * @param $file
     * @param $pathName
     * @return mixed|string
     */
    public function uploadFile($file, $pathName): string
    {
        $path = $file->storePublicly($pathName, 's3');
        Storage::disk('s3')->url($path);
        $file = explode('/', $path);
        return $file[array_key_last($file)];
    }

    /**
     * function server Local storage files
     * @param $folder
     * @param $image
     * @return string
     */
    public function UploadImage($folder, $image): string
    {
        $extension = $image->getClientOriginalExtension();
        $fileName = 'img_'. time() . '_' . uniqid() . '.' . $extension;
       //  $image->move(public_path('images/' . $folder), $fileName);
        $image->storeAs('public/images/' . $folder, $fileName);
        return $fileName;
    }

    /**
     * @param $modelName
     * @param $type
     * @param $image
     * @param $dataId
     * @return string
     */
    public function saveImage($modelName, $type, $image, $dataId): string
    {
        $dataIdd = $this->getModelKey($modelName);
        $fileName = $this->UploadImage($modelName, $image);
        $model = '\\App\\Models\\' . $modelName;
        /** save image */
        $image = new $model;
        $image->image = $fileName;
        $image->file_type_id = $type;
        $image->$dataIdd = $dataId;
        if ($image->save()) {
            return $this->apiResponse('', $image);
        }
        return $this->apiResponse('Failed to save', null, 500);
    }

    /**
     * @param $model
     * @return string |void
     */
    private function getModelKey($model): string
    {
        $modelArray = [
            'ZoneFile' => 'zone_id',
            'UserFile' => 'user_id',
            'SayesFile' => 'user_id'
        ];

        foreach ($modelArray as $key => $value) {
            if ($key === $model) {
                return $value;
            }
        }
    }

    public function updateImage($modelName, $type, $image, $dataId, $id): string
    {

        $dataIdd = $this->getModelKey($modelName);
        $fileName = $this->UploadImage($modelName, $image);
        $model = '\\App\\Models\\' . $modelName;
        /** save image */
        $image = $model::find($id);
        $image->image = $fileName;
        $image->file_type_id = $type;
        $image->$dataIdd = $dataId;
        $image->save();
        if ($image->save()) {
            return $this->apiResponse('', $image);
        }
        return $this->apiResponse('Failed to update', null, 500);
    }

    /**
     * @param $model
     * @param $id
     */
    public function removeImage($model, $id)
    {
        $modelName = '\\App\\Models\\' . $model;
        $file = $modelName::find($id);
        File::delete(public_path("images/" . $model . "/" . $file->image));
        $file->delete();
    }
}
