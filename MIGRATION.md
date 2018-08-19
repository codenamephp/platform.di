# Migraiton

## 3.x

### PHP-DI

The biggist change is the support of php-di 6 and the dropped support of php-di 5. The migration is somwhat huge
depending on the number of defintions. See http://php-di.org/doc/migration/6.0.html for details

### API

- All occurances of `Interop\Container\ContainerInterface` have been replaced with `\Psr\Container\ContainerInterface` so updated your usages accordingly
- The types of `\de\codenamephp\platform\di\Container::__construct` have changed so if you instantiate the container yourself (you shouldn't, that's what the 
  builder is for) you have to update the type of the first and 3rd parameter
- All parameters of `\de\codenamephp\platform\di\Container::__construct` are now optoinal so if you just passed them because you had to, remove them

### PHP

PHP7 is now required for php-di 6 and type hints