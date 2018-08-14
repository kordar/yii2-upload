<?php
namespace kordar\upload\oss;

use OSS\OssClient;

class OssHelper
{
    /**
     * @return OssClient
     */
    public static function getOssInstance()
    {
        return new OssClient(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_REGION);
    }
}