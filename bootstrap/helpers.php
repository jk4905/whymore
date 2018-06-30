<?php

function getImgUrl($url)
{
    if (empty($url)) {
        return '';
    }
    $disk = \Illuminate\Support\Facades\Storage::disk('qiniu');

    if (\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) {
        return $url;
    }
    return $disk->url($url);
}