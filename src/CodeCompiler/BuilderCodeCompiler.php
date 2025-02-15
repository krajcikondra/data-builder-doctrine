<?php

declare(strict_types=1);

namespace Krajcik\DataBuilderDoctrine\CodeCompiler;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Krajcik\DataBuilder\Dto\BuilderToGenerateDto;
use Krajcik\DataBuilderDoctrine\Exception\EntityNotFoundNoPrimaryKeyException;
use Krajcik\DataBuilderDoctrine\Reflection\DoctrineEntity;
use Nette\PhpGenerator\ClassType;
use Krajcik\DataBuilder\CodeCompiler\BuilderCodeCompiler as CoreBuilderCodeCompiler;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;

class BuilderCodeCompiler extends CoreBuilderCodeCompiler
{
    public function precompile(BuilderToGenerateDto $data): ClassType
    {
        $class = parent::precompile($data);

        $class->addProperty('em')
            ->setPrivate()
            ->setType(EntityManagerInterface::class);

        $class->removeProperty('db');

        return $class;
    }

    protected function precompileNamespace(BuilderToGenerateDto $data): PhpNamespace
    {
        $namespace = parent::precompileNamespace($data);
        $namespace->addUse(EntityManagerInterface::class);
        $namespace->addUse(ResultSetMapping::class);
        $namespace->addUse(EntityNotFoundNoPrimaryKeyException::class);
        $namespace->addUse(UniqueConstraintViolationException::class);
        $namespace->addUse(DoctrineEntity::class);
        return $namespace;
    }


    protected function createConstructMethod(
        ClassType $class,
        BuilderToGenerateDto $data,
    ): Method {
        $parameterType = $this->pathResolver->getParameterClassName($data->getClassName());
        $method = $class->addMethod('__construct')
            ->setPublic();

        $method
            ->addParameter('parameters')
            ->setType($parameterType);

        $method
            ->addParameter('em')
            ->setType(EntityManagerInterface::class);

        $method->addBody('$this->parameters = $parameters;');
        $method->addBody('$this->em = $em;');
        return $method;
    }

    protected function createBuildAndSaveMethod(
        ClassType $class,
        BuilderToGenerateDto $data,
    ): Method {
        $method = $class->addMethod('buildAndSave')
            ->setPublic()
            ->setReturnType($data->getFullClassName());
        $method
            ->addParameter('ignoreUniqueViolation', false)
            ->setType('bool');

        $method->addBody(sprintf('$rsm = new ResultSetMapping();'));
        $method->addBody('$parameterNames = array_map(fn(string $name) => ":" . $name, array_keys($this->getData()));');
        $method->addBody('$sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)", "' . $data->getTableName() . '",
             implode(", ", array_keys($this->getData())),
             implode(", ", $parameterNames) );');
        $method->addBody('$stmt = $this->em->createNativeQuery($sql, $rsm);');

        $method->addBody('foreach ($this->getData() as $name => $value) {');
        $method->addBody('    $stmt->setParameter("$name", $value);');
        $method->addBody('}');



        $primaryKey = $this->dbContext->getStructure()->getPrimaryKey($data->getTableName());
        $method->addBody('$data = $this->getData();');
        $method->addBody(sprintf('$primaryKey = %s;', $primaryKey ? "'$primaryKey'" : "null"));

        $method->addBody('try {');
        $method->addBody('    $stmt->execute();');
        $method->addBody('} catch (UniqueConstraintViolationException $e) {');


        $method->addBody('    if ($ignoreUniqueViolation === false) {');
        $method->addBody('        throw $e;');
        $method->addBody('    }');

        $method->addBody(sprintf('    return $this->em->getRepository(%s::class)->findOneBy([', $data->getClassName()));
        $method->addBody(sprintf(
            '        DoctrineEntity::getPropertyName(%s::class, $primaryKey) => $data[$primaryKey]',
            $data->getClassName()
        ));
        $method->addBody('    ]);');


        $method->addBody('}');

        $method->addBody('if (isset($data[$primaryKey]) === true) {');
        $method->addBody('    $id = $data[$primaryKey];');
        $method->addBody('} else {');
        $method->addBody('    $id = $this->em->getConnection()->lastInsertId($primaryKey);');
        $method->addBody('}');


        $method->addBody(sprintf('$entity = $this->em->getRepository(%s::class)->find($id);', $data->getClassName()));
        $method->addBody(sprintf('if ($entity === null) {'));
        // phpcs:disable
        $method->addBody(sprintf(
            '    throw new EntityNotFoundNoPrimaryKeyException("Cannot find record \"" . $id . "\" in table \"%s\"");',
            $data->getClassName(),
        ));
        // phpcs:enable
        $method->addBody(sprintf('}'));


        $method->addBody(sprintf('assert($entity instanceof %s);', $data->getClassName()));
        $method->addBody('return $entity;');
        return $method;
    }

    protected function appendDbParameterToMethods(Method $method): void
    {
        $method->addBody('    ),');
        $method->addBody('    $this->em,');
        $method->addBody(');');
    }
}
