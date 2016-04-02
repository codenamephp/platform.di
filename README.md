# platform.di

Simple dependency injection container based on <a href="http://www.php-di.org" target="_blank">www.php-di.org</a>

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
