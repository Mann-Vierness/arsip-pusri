<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class MinioHelper
{
    /**
     * Get metadata (size, content-type, etc) for a file in MinIO using AWS SDK.
     * @param string $path
     * @return array|null
     */
    public static function getFileMetadata($path)
    {
        try {
            $disk = Storage::disk('minio');
            $client = $disk->getClient();
            $bucket = config('filesystems.disks.minio.bucket');
            $result = $client->headObject([
                'Bucket' => $bucket,
                'Key' => $path,
            ]);
            return [
                'size' => $result['ContentLength'] ?? null,
                'content_type' => $result['ContentType'] ?? null,
                'etag' => $result['ETag'] ?? null,
                'last_modified' => $result['LastModified'] ?? null,
                'raw' => $result,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
