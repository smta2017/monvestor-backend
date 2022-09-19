<?php

namespace App\Http\Traits;

use App\Models\ZoneFile;

trait ZoneFileTrait
{
    use FileUploaderTrait;

    /**
     * @param $imageArray
     * @param $ownershipImage
     * @param $zoneId
     * @param $status
     * @return bool
     */
    private function uploadZoneFile($imageArray, $ownershipImage, $zoneId, $status): bool
    {
        foreach ($imageArray as $image) {
            if (is_file($image)) {
                $imageName = $this->UploadImage('ZoneFile', $image);
                $file = ZoneFile::create([
                    'file_type_id' => 4,
                    'zone_id' => $zoneId,
                    'image' => $imageName,
                    'status' => $status
                ]);
                if (!$file)
                    return false;
            }
        }
        if (is_file($ownershipImage)) {
            $imageName = $this->UploadImage('ZoneFile', $ownershipImage);
            $file = ZoneFile::create([
                'file_type_id' => 8,
                'zone_id' => $zoneId,
                'image' => $imageName,
                'status' => $status
            ]);
            if (!$file)
                return false;
        }
        return true;
    }
}
