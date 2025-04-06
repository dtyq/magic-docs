<?php

declare(strict_types=1);
/**
 * Copyright (c) The Magic , Distributed under the software license
 */

namespace Dtyq\CloudFile\Hyperf;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of cloud-file-sdk.',
                    'source' => __DIR__ . '/publish/cloudfile.php',
                    'destination' => BASE_PATH . '/config/autoload/cloudfile.php',
                ],
            ],
        ];
    }
}
