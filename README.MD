# krajcik/data-builder-doctrine

Package helps to write test data for tests using data builders which package generate specifically for your project.
By default, are all data filled randomly with option change one or more properties.

## Installation

```
composer require krajcik/data-builder-doctrine
```

## Using

### Configuration and generate

```
use Doctrine\ORM\EntityManagerInterface;
use Krajcik\DataBuilderDoctrine\BuilderCompiler;
use Krajcik\DataBuilderDoctrine\Dto\Configuration;


// load your application
$app = require __DIR__ . '/../public/index.php';
$em = $app->getService(EntityManagerInterface::class); // entty manager from your project

$targetFolder = __DIR__ . '/Builder/Generated';
$configuration = new Configuration(
    $targetFolder,
    namespace: 'Tests\Builder\Generated',
    customBuilderFolder: __DIR__ . '/Builder/CustomBuilder',
);

```


### Generate builders
```
    use \Krajcik\DataBuilder\BuilderCompiler;

    $compiler = new BuilderCompiler($em, $configuration);
    $compiler->compile();
```

### Using generated builders in your code

```
    $builderFactory = new \Tests\Generated\Builder\BuilderFactory($entityManager);

    $contactData = $builderFactory->createContactDataBuilder()
        ->withFirstName('John')
        ->withLastName('Doe')
        ->buildAndSave();
    // $contactData is saved doctrine entity (Api\Contact\Entity\ContactData) from your project which you can use now in your test case

```
