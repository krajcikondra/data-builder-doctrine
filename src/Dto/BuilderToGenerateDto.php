<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Dto;

use Krajcik\DataBuilderDoctrine\Reflection\DoctrineEntity;
use Krajcik\DataBuilder\Dto\BuilderToGenerateDto as CoreBuilderToGenerateDto;

final class BuilderToGenerateDto extends CoreBuilderToGenerateDto
{
    public static function createFromDoctrineEntity(string $fullClassName): self
    {
        return new self(
            DoctrineEntity::getTableName($fullClassName),
            $fullClassName,
            DoctrineEntity::getPropertyAndDbNameList($fullClassName),
        );
    }
}
