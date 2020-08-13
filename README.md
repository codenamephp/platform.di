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

This creates a builder without definitions. To add definitions I recommend using on of the provider options below, especially
the `de\codenamephp\platform\di\definitionsProvider\iArray` provider.

From there you just get your dependencies from the container.

### Using providers

The best way to have configurations within modules and libraries is via providers. This way, the provider class will be used to add the files, glob paths or definitions. Every time
the provider class is updated, the configuration will be upgraded as well

All providers need to implement one of the de\codenamephp\platform\di\definitionsProvider\* interfaces

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

This interface declares that a provider depends on other providers and must implement the getDependencies() method which returns all the class names of providers that have to
be added to the container before this provider can be added.

```php
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

class MustBeAddedBeforeMe implements iDefintionsProvider {}

class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn {

  public function getDependencies() : array {
    return [MustBeAddedBeforeMe::class ];
  }
}
```
##### iCoversDependency

This interface declares that a provider covers one or multiple dependencies, e.g. if you built a custom provider that covers multiple other packages. You must implement the
getCoveredDependencies() method that returns all class names of providers that this provider covers (including its own).

```php
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

class OtherDependencyThatIsNowCovered implements iDefintionsProvider{}

class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies {

  public function getCoveredDependencies() : array {
    return [OtherDependencyThatIsNowCovered::class];
  }
}
```

#### Dependency Handlers

The ContainerBuilder has a DependencyHandler that handles the depdency checks and keeps track of already added dependencies and must implement the
`de/codenamephp/platform/di/definitionsProvider/dependency/handler/iHandler`. By default, a [de/codenamephp/platform/di/definitionsProvider/dependency/handler/DontHandle](#donthandle)
instance is set so there is no dependency handling active by default (so BC is kept). This will change in future releases.

#### DontHandle

This handler actually doesn't do anything and is the default handler set in the ContainerBuilder. This handler is used to deactivate the feature.

#### ClassNamesInArray

This handler collects the class names of depdencies in an array and checks the dependencies against them. If the [iDependsOn](#idependson) interface is not added to the provider, the class
name of the provider is added automaticly, so if your provider only covers it's own depdendency, you don't need to implement the interface.
