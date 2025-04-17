<?php

declare(strict_types=1);
/**
 * Copyright (c) The Magic , Distributed under the software license
 */

namespace Dtyq\CloudFile\Kernel\Utils\SimpleUpload;

use Dtyq\CloudFile\Kernel\Exceptions\CloudFileException;
use Dtyq\CloudFile\Kernel\Struct\AppendUploadFile;
use Dtyq\CloudFile\Kernel\Struct\UploadFile;
use Dtyq\CloudFile\Kernel\Utils\CurlHelper;
use Dtyq\CloudFile\Kernel\Utils\SimpleUpload;
use Throwable;

class ObsSimpleUpload extends SimpleUpload
{
    /**
     * 上传到华为云.
     */
    public function uploadObject(array $credential, UploadFile $uploadFile): void
    {
        if (isset($credential['temporary_credential'])) {
            $credential = $credential['temporary_credential'];
        }
        if (! isset($credential['dir'])
            || ! isset($credential['policy'])
            || ! isset($credential['host'])
            || ! isset($credential['AccessKeyId'])
            || ! isset($credential['signature'])
        ) {
            throw new CloudFileException('Obs upload credential is invalid');
        }

        $key = $credential['dir'] . $uploadFile->getKeyPath();
        $body = [
            'key' => $key,
            'policy' => $credential['policy'],
            'AccessKeyId' => $credential['AccessKeyId'],
            'signature' => $credential['signature'],
            'file' => curl_file_create($uploadFile->getRealPath(), $uploadFile->getMimeType(), $uploadFile->getName()),
        ];
        if (! empty($credential['content_type'])) {
            $body['content-Type'] = $credential['content_type'];
        }

        try {
            CurlHelper::sendRequest($credential['host'], $body, [], 204);
        } catch (Throwable $exception) {
            $errorMsg = $exception->getMessage();
            throw $exception;
        } finally {
            if (isset($errorMsg)) {
                $this->sdkContainer->getLogger()->warning('simple_upload_fail', ['key' => $key, 'host' => $credential['host'], 'error_msg' => $errorMsg]);
            } else {
                $this->sdkContainer->getLogger()->info('simple_upload_success', ['key' => $key, 'host' => $credential['host']]);
            }
        }
        $uploadFile->setKey($key);
    }

    public function appendUploadObject(array $credential, AppendUploadFile $appendUploadFile): void
    {
        throw new CloudFileException('Tos does not support append upload');
    }
}
