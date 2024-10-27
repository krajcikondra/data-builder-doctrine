<?php

declare(strict_types=1);

namespace Unit\Reflection;

require __DIR__ . '/Data/MyTestEntity.php';

use Krajcik\DataBuilderDoctrine\Reflection\DoctrineEntity;
use PHPUnit\Framework\TestCase;
use Unit\Reflection\Data\MyTestEntity;

final class DoctrineEntityTest extends TestCase
{
    public function testGetTableName(): void
    {
        $tableName = DoctrineEntity::getTableName(MyTestEntity::class);
        $this->assertSame('my_test_table', $tableName);
    }

    public function testGetPropertyAndDbNameList(): void
    {
        $propertyList = DoctrineEntity::getPropertyAndDbNameList(MyTestEntity::class);
        $this->assertSame([
            'id' => 'id',
            'nazev' => 'name',
            'price' => 'price',
        ], $propertyList);
    }

    public function testGetPropertyName(): void
    {
        $propertyName = DoctrineEntity::getPropertyName(MyTestEntity::class, 'nazev');
        $this->assertSame('name', $propertyName);
    }
}
