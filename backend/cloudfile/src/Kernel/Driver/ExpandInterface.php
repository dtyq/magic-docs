<?php

declare(strict_types=1);
/**
 * Copyright (c) The Magic , Distributed under the software license
 */

namespace Dtyq\CloudFile\Kernel\Driver;

use Dtyq\CloudFile\Kernel\Struct\CredentialPolicy;
use Dtyq\CloudFile\Kernel\Struct\FileLink;
use Dtyq\CloudFile\Kernel\Struct\FileMetadata;
use Dtyq\CloudFile\Kernel\Struct\FilePreSignedUrl;

interface ExpandInterface
{
    public function getUploadCredential(CredentialPolicy $credentialPolicy, array $options = []): array;

    /**
     * @return array<string, FilePreSignedUrl>
     */
    public function getPreSignedUrls(array $fileNames, int $expires = 3600, array $options = []): array;

    /**
     * @return array<FileMetadata>
     */
    public function getMetas(array $paths, array $options = []): array;

    /**
     * @return array<FileLink>
     */
    public function getFileLinks(array $paths, array $downloadNames = [], int $expires = 3600, array $options = []): array;

    public function destroy(array $paths, array $options = []): void;

    public function duplicate(string $source, string $destination, array $options = []): string;
}
