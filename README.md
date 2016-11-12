# platform.di

Simple dependency injection container based on <a href="http://www.php-di.org" target="_blank">www.php-di.org</a>

## Installation

Easiest way is via composer:

```json
  "require": {
    "codenamephp/platform.di": "*"
  }
```

##Usage

```php
$builder = new de\codenamephp\platform\di\ContainerBuilder();
$builder->addGlobPath('path/to/definitions/{{,*.}global,{,*.}local}.php');
$container = $builder->build();
$container->get('...');
```

This creates a builder that will add all definition files in the path/to/definitions folder in the order
>* global.php
>* *.global.php
>* local.php
>* *.local.php

which allows you define your overall global config in the global files and override them on a system/environment basis
where you should add the *local files to the ignore list of your VCS.

###Using providers

The best way to have configurations within modules and libraries is via providers. This way, the provider class will be used to add the files, glob paths or definitions. Every time
the provider class is updated, the configuration will be upgraded as well

All providers need to implement one of the de\codenamephp\platform\di\definitionsProvider\* interfaces

```php
$builder = new de\codenamephp\platform\di\ContainerBuilder();
$builder->addDefinitionsByProvider(new DefinitionsProvider());
$container = $builder->build();
$container->get('...');
```

####Array

Probably the most performant provider since the definitions are defined within the method and don't require any additional file lookups:

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\iArray {

  public function getDefinitions() {
    return ['some class' => 'some defintion'];
  }
}
```

####File

The file provider provides absolute file paths to definition files:

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\iFiles {

  public function getFiles() {
    return [__DIR__ . '/path/to/file'];
  }
}
```

####GlobPaths

The globPaths provider provides glob patterns that find definition files. These glob paths will be added just like the manual glob path adding:

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\iGlobPaths {

  public function getGlobPaths() {
    return [__DIR__ . '/path/to/definitions/{{,*.}global,{,*.}local}.php'];
  }
}
```
### Provider Dependencies

Providers can depend on other providers, e.g. to override their definitions. If that is the case, providers can implement on of the
de\codenamephp\platform\di\definitionsProvider\dependency\* interfaces.

#### Dependency Providers

##### iDependsOn

This interface declares that a provider depends on other providers and must implement the getDependencies() method which returns all the class names of providers that have to
be added to the container before this provider can be added.

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn {

  public function getDependencies() {
    return [\other\provider\that\must\be\added\before\Me::class];
  }
}
```
##### iCoversDependency

This interface declares that a provider covers one or multiple dependencies, e.g. if you built a custom provider that covers multiple other packages. You must implement the
getCoveredDependencies() method that returns all class names of providers that this provider covers (including its own).

```php
class DefintionsProvider implements de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies {

  public function getCoveredDependencies() {
    return [\ohter\dependency\that\now\need\not\to\Added::class];
  }
}
```

#### Dependency Handlers

The ContainerBuilder has a DependencyHandler that handles the depdency checks and keeps track of already added dependencies and must implement the
e/codenamephp/platform/di/definitionsProvider/dependency/handler/iHandler. By default, a [de/codenamephp/platform/di/definitionsProvider/dependency/handler/DontHandle](#DontHandle)
instance is set so there is no dependency handling active by default (so BC is kept). This will change in future releases.

#### DontHandle

This handler actually doesn't do anything and is the default handler set in the ContainerBuilder. This handler is used to deactivate the feature.

#### ClassNamesInArray

This handler collects the class names of depdencies in an array and checks the dependencies against them. If the [iDependsOn](#iDependsOn) interface is not added to the provider, the class
name of the provider is added automaticly, so if your provider only covers it's own depdendency, you don't need to implement the interface.
