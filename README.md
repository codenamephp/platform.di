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
