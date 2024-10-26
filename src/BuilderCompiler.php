<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine;

use Doctrine\ORM\EntityManagerInterface;
use Krajcik\DataBuilder\CodeCompiler\Extension\FactoryCodeExtension;
use Krajcik\DataBuilderDoctrine\Builder\DoctrineBuilderCompiler;
use Krajcik\DataBuilderDoctrine\Dto\Configuration;
use Krajcik\DataBuilderDoctrine\Loader\DoctrineEntityLoader;

final class BuilderCompiler
{
    private DoctrineBuilderCompiler $builderCompiler;

    public function __construct(
        private EntityManagerInterface $entityManager,
        Configuration $configuration,
    ) {
        $this->builderCompiler = new DoctrineBuilderCompiler($this->entityManager, $configuration);
    }

    public function compile(): void
    {
        $loader = new DoctrineEntityLoader($this->entityManager);
        $buildersToGenerateDto = $loader->load();
        $this->builderCompiler->compile($buildersToGenerateDto);
    }

    public function setExtension(FactoryCodeExtension $extension): void
    {
        $this->builderCompiler->setExtension($extension);
    }
}
