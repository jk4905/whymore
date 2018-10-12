<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * 上传
     * @param UploadedFile $image
     * @return false|string
     * @throws InvalidRequestException
     */
    public static function uploadOne(UploadedFile $image)
    {
        if (!$image->isValid()) {
            throw new InvalidRequestException(40005);
        }
        $path = $image->store('/');

        $realPath = public_path('upload/') . $path;
        $disk = Storage::disk('qiniu');

        $ret = $disk->put($path, file_get_contents($realPath));
        if (!$ret) {
            throw new InvalidRequestException(40005);
        }
//        $url = $disk->url($path);
//        删除本地文件
        unlink($realPath);
        return $path;
    }

    public static function uploadMany(UploadedFile $images)
    {
        $paths = [];
        if (!is_array($images)) {
            $images = [$images];
        }
        foreach ($images as $image) {
            $paths[] = $image;
        }
        return $paths;
    }
}