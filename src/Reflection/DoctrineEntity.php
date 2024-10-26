<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Reflection;

use Doctrine\ORM\Mapping\Table;
use ReflectionClass;

final class DoctrineEntity
{
    public static function getTableName(string $fullClassName): string
    {
        $reflection = new ReflectionClass($fullClassName);
        $attributes = $reflection->getAttributes(Table::class);
        return $attributes[0]->getArguments()['name'];
    }

    /**
     * @return array<string, string>
     */
    public static function getPropertyAndDbNameList(string $fullClassName): array
    {
        $values = [];
        $reflection = new ReflectionClass($fullClassName);

        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes('Doctrine\ORM\Mapping\Column') as $attribute) {
                $arguments = $attribute->getArguments();
                if (array_key_exists('name', $arguments) === false) {
                    $values[$property->getName()] = $property->getName();
                } else {
                    $columnDbName = $attribute->getArguments()['name'];
                    $values[$columnDbName] = $property->getName();
                }
            }
        }

        return $values;
    }
}
