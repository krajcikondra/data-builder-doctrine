<?php

declare(strict_types=1);

use Krajcik\DataBuilderDoctrine\BuilderCompiler;
use Krajcik\DataBuilderDoctrine\Dto\Configuration;
use Nette\Loaders\RobotLoader;

require __DIR__ . '/../vendor/autoload.php';

function compile(): void
{
    $targetFolder = __DIR__ . '/../temp/tests/Generated';

    $configuration = new Configuration(
        $targetFolder,
        'Tests\Builder\Generated'
    );

    $compiler = new BuilderCompiler($entityManager, $configuration);
    $compiler->compile();
}


$loader = new RobotLoader();
$loader
    ->addDirectory(__DIR__ . '/../src/')
    ->addDirectory(__DIR__ . '/../../../blogic/mydock/moduleApi/')
    ->setTempDirectory(__DIR__ . '/../temp/')
    ->register();

set_time_limit(-1);
ini_set('memory_limit', '-1');
error_reporting(E_ALL);

compile();

echo "[DONE]\n";
