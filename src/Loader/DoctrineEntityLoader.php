<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Krajcik\DataBuilderDoctrine\Dto\BuilderToGenerateDto;

final class DoctrineEntityLoader
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return BuilderToGenerateDto[]
     */
    public function load(): array
    {
        $buildersToGenerateDto = [];
        foreach ($this->getAllEntityClassNames() as $entityClassName) {
            $buildersToGenerateDto[] = BuilderToGenerateDto::createFromDoctrineEntity($entityClassName);
        }
        return $buildersToGenerateDto;
    }

    /**
     * @return string[]
     */
    private function getAllEntityClassNames(): array
    {
        $metadataFactory = $this->entityManager->getMetadataFactory();

        $allMetadata = $metadataFactory->getAllMetadata();
        $entityClasses = [];

        foreach ($allMetadata as $metadata) {
            $entityClasses[] = $metadata->getName();
        }

        return $entityClasses;
    }
}
