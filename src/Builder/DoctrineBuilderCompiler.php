<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\Builder;

use Doctrine\ORM\EntityManagerInterface;
use Krajcik\DataBuilder\BuilderCompiler as CoreBuilderCompiler;
use Krajcik\DataBuilder\Dto\Configuration as BaseConfiguration;
use Krajcik\DataBuilderDoctrine\CodeCompiler\BuilderCodeCompiler;
use Krajcik\DataBuilderDoctrine\Dto\Configuration;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

final class DoctrineBuilderCompiler extends CoreBuilderCompiler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        Configuration $configuration,
    )
    {
        $dbParams = $this->entityManager->getConnection()->getParams();

        $coreConfiguration = new BaseConfiguration(
            targetFolder: $configuration->getTargetFolder(),
            dbHost: $dbParams['host'],
            dbName: $dbParams['dbname'],
            dbUser: $dbParams['user'],
            dbPassword: $dbParams['password'],
            namespace: $configuration->getNamespace(),
        );

        parent::__construct($coreConfiguration);
        $this->builderCompiler = new BuilderCodeCompiler($this->db, $this->pathResolver, $coreConfiguration);
    }

    protected function generateCreateBuilderMethod(ClassType $builderFactoryClass, string $entityClass): Method
    {
        $method = $builderFactoryClass->addMethod(sprintf('create%sBuilder', $entityClass));
        $method->setReturnType($this->pathResolver->getBuilderNamespace($entityClass));
        $method
            ->addParameter('parameters')
            ->setNullable()
            ->setDefaultValue(null)
            ->setType($this->pathResolver->getParameterClassName($entityClass));

        $method->addBody('if ($parameters === null) {');
        $method->addBody(sprintf(
            '    $parameters = (new %s($this->getFaker()))->createDefaultParameters();',
            $this->pathResolver->getFactoryNamespace($entityClass),
        ));
        $method->addBody('}');
        $method->addBody(sprintf(
            'return new %s($parameters, $this->em);',
            $this->pathResolver->getBuilderNamespace($entityClass),
        ));
        return $method;
    }

    protected function generateBuilderFactoryConstructor(
        Method $builderFactoryConstructor,
        ClassType $builderFactoryClass
    ): void {
        $builderFactoryConstructor->addBody('$this->em = $em;');
        $builderFactoryConstructor->addBody('$this->faker = null;');

        $builderFactoryConstructor
            ->addParameter('em')
            ->setType(EntityManagerInterface::class);

        $builderFactoryClass
            ->addProperty('em')
            ->setPrivate()
            ->setType(EntityManagerInterface::class);
    }

}