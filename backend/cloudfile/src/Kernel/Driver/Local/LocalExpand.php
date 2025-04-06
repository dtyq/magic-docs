<?php

declare(strict_types=1);
/**
 * Copyright (c) The Magic , Distributed under the software license
 */

namespace Dtyq\CloudFile\Kernel\Driver\Local;

use Dtyq\CloudFile\Kernel\Driver\ExpandInterface;
use Dtyq\CloudFile\Kernel\Exceptions\CloudFileException;
use Dtyq\CloudFile\Kernel\Struct\CredentialPolicy;
use Dtyq\CloudFile\Kernel\Struct\FileLink;
use Dtyq\CloudFile\Kernel\Struct\FileMetadata;
use League\Flysystem\FileAttributes;

class LocalExpand implements ExpandInterface
{
    private array $config = [];

    public function __construct(array $config = [])
    {
        if (empty($config['read_host'])) {
            throw new CloudFileException('read_host is required');
        }
        if (empty($config['write_host'])) {
            throw new CloudFileException('write_host is required');
        }
        if (empty($config['root'])) {
            throw new CloudFileException('root is required');
        }
        $this->config = $config;
    }

    public function getUploadCredential(CredentialPolicy $credentialPolicy, array $options = []): array
    {
        return [
            'host' => $this->config['write_host'],
            'dir' => $credentialPolicy->getDir(),
        ];
    }

    public function getPreSignedUrls(array $fileNames, int $expires = 3600, array $options = []): array
    {
        return [];
    }

    public function getMetas(array $paths, array $options = []): array
    {
        $metas = [];
        foreach ($paths as $path) {
            $fullPath = $this->config['root'] . '/' . ltrim($path, '/');
            if (file_exists($fullPath)) {
                $fileAttributes = new FileAttributes(
                    path: $path,
                    fileSize: filesize($fullPath),
                    visibility: is_readable($fullPath) ? 'public' : 'private',
                    lastModified: filemtime($fullPath),
                    mimeType: mime_content_type($fullPath),
                );
                $metas[] = new FileMetadata(
                    name: basename($path),
                    path: $path,
                    fileAttributes: $fileAttributes,
                );
            }
        }
        return $metas;
    }

    public function getFileLinks(array $paths, array $downloadNames = [], int $expires = 3600, array $options = []): array
    {
        $links = [];
        foreach ($paths as $index => $path) {
            $fullPath = $this->config['root'] . '/' . ltrim($path, '/');
            if (file_exists($fullPath)) {
                $downloadName = $downloadNames[$index] ?? basename($path);
                $links[$path] = new FileLink(
                    path: $path,
                    url: $this->config['read_host'] . '/' . ltrim($path, '/'),
                    expires: $expires,
                    downloadName: $downloadName,
                );
            }
        }
        return $links;
    }

    public function destroy(array $paths, array $options = []): void
    {
        foreach ($paths as $path) {
            $fullPath = $this->config['root'] . '/' . ltrim($path, '/');
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    public function duplicate(string $source, string $destination, array $options = []): string
    {
        $sourcePath = $this->config['root'] . '/' . ltrim($source, '/');
        $destinationPath = $this->config['root'] . '/' . ltrim($destination, '/');

        if (file_exists($sourcePath)) {
            $dir = dirname($destinationPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            copy($sourcePath, $destinationPath);
            return $destination;
        }

        return '';
    }
}
