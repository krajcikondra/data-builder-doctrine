<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Dto;

final class Configuration
{
    public function __construct(
        private string $targetFolder,
        private string $namespace = 'Tests\Builder\Generated',
    ) {}

    public function getTargetFolder(): string
    {
        return $this->targetFolder;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}