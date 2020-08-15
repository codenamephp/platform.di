# platform.di

Simple dependency injection container based on <a href="http://www.php-di.org" target="_blank">www.php-di.org</a>

## Installation

Easiest way is via composer. Just run `composer require codenamephp/platform.di` in your cli which should install the latest version for you.

## Usage

```php
$builder = new de\codenamephp\platform\di\ContainerBuilder();
$container = $builder->build();
$container->get('...');
```

This creates a builder without definitions. To add definitions I recommend using one of the provider options below, especially
the `de\codenamephp\platform\di\definitionsProvider\iArray` provider.

From there you just get your dependencies from the container.

### Using providers

The best way to have configurations within modules and libraries is via providers. This way, the provider class will be used to add the files or definitions. 
Every time the provider class is updated, the configuration will be updated as well.

All providers need to implement one of the `de\codenamephp\platform\di\definitionsProvider\*` interfaces

```php
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

$builder = new de\codenamephp\platform\di\ContainerBuilder();
$builder->addDefinitionsByProvider(new class() implements iDefintionsProvider{});
$container = $builder->build();
$container->get('...');
```

#### Array

Probably the most performant provider since the definitions are defined within the method and don't require any additional file lookups:

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\iArray {

  public function getDefinitions() : array {
    return ['some class' => 'some defintion'];
  }
}
```

#### File

The file provider provides absolute file paths to definition files:

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\iFiles {

  public function getFiles() : array {
    return [__DIR__ . '/path/to/file'];
  }
}
```

#### MetaProvider

Sometimes you want to split dependencies into multiple providers so they don't get too long and/or to group them into logical units. But you don't want to add 
multiple providers to the actual project that uses the provider. This is what the `\de\codenamephp\platform\di\definitionsProvider\iMetaProvider` interface is
for. It basically creates multiple providers and returns them as array which are then added by the container builder like any other provider including 
dependency checks and nesting other meta providers.

```php
use de\codenamephp\platform\di\definitionsProvider\iArray;
use de\codenamephp\platform\di\definitionsProvider\iFiles;
use de\codenamephp\platform\di\definitionsProvider\iMetaProvider;

class MyArrayProvider implements iArray{
    public function getDefinitions() : array {
     return [];
    }
}
class MyFileProvider implements iFiles {
    public function getFiles() : array {
      return []; 
    }
}
class MyNestedMetaProvider implements iMetaProvider {
    public function getProviders() : array{
      return [new MyFileProvider()]; 
    }
}
class MyMetaProvider implements iMetaProvider {
  public function getProviders() : array {
    return [
        new MyArrayProvider(),
        new MyNestedMetaProvider()
    ];
  }
}
```

But even in this example it becomes clearly visible that we are dealing with an Uncle Ben situation here: With great recursion comes bad headache!
Dependencies are still checked so the providers need to be in the correct order which can become a real pain real fast if you go crazy with nesting providers.

I recommend not to use more than one level of nesting and if possible avoid it all together. After all, it's just a side effect of the implementation rather than
a planned feature. ;)

### Provider Dependencies

Providers can depend on other providers, e.g. to override their definitions. If that is the case, providers can implement on of the
`de\codenamephp\platform\di\definitionsProvider\dependency\*` interfaces.

#### Dependency Providers

##### iDependsOn

This interface declares that a provider depends on other providers and must implement the getDependencies() method which returns all the class names of 
providers that have to be added to the container before this provider can be added.

```php
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

class MustBeAddedBeforeMe implements iDefintionsProvider {}

class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn {

  public function getDependencies() : array {
    return [MustBeAddedBeforeMe::class ];
  }
}
```

### Dependency checks

When you have modules that depend on each other most often the defintions depend on each other as well. This is what the dependency collections are for.
They collect the providers (duh) and also check the dependencies using the interfaces from above. They implement the 
`\de\codenamephp\platform\di\definitionsProvider\collection\iCollection` and do different levels of checks and sorting.

#### SimpleArray

This collection doesn't actually do any dependency checks and just collects the providers and stores them in an array. This is used in most other
collection as a simple storage.

#### ClassNamesInArray

This collection collects the class names of depdencies in an array and checks the dependencies against them. If the [iDependsOn](#idependson) interface is
not added to the provider, the class name of the provider is added automaticly, so if your provider only covers it's own depdendency, you don't need to 
implement the interface.

This is a very simple check so it's also easy to debug. The dependencies are checked every time you add a dependency so it will fail early if something is 
missing. The drawback of this is that you have to add the providers in the correct order.

```php
use de\codenamephp\platform\di\ContainerBuilder;
use de\codenamephp\platform\di\definitionsProvider\collection\ClassNamesInArray;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

class Dependency implements iDefintionsProvider {}
class Dependant implements iDependsOn { public function getDependencies() : array{ return [Dependency::class]; } }

$collection = new ClassNamesInArray();
$collection->add(new Dependency());
$collection->add(new Dependant()); // would fail if those were reversed
//...

$containerBuilder = new ContainerBuilder();
foreach($collection->get() as $provider) { $containerBuilder->addDefinitionsByProvider($provider); }
$container = $containerBuilder->build();
//...
```

#### TopoGraph

This collection sorts the provides by their dependencies. The sort and check is performed once you get the providers. This enables you to add the providers
in any way you see fit. But it also means that there's a slight performance overhead and debugging might be a bit harder.

It also means that you have no way to influence the sequence other than declaring the dependencies so this is not only recommended but alomst
necessary.

```php
use de\codenamephp\platform\di\ContainerBuilder;
use de\codenamephp\platform\di\definitionsProvider\collection\TopoGraph;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

class Dependency implements iDefintionsProvider {}
class Dependant implements iDependsOn { public function getDependencies() : array{ return [Dependency::class]; } }

$collection = new TopoGraph();
$collection->add(new Dependant()); // the sequence doesn't matter
$collection->add(new Dependency());
//...

$containerBuilder = new ContainerBuilder();
foreach($collection->get() as $provider) { $containerBuilder->addDefinitionsByProvider($provider); } // Dependency will be returned/added first
$container = $containerBuilder->build();
//...
```