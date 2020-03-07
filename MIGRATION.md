# Migraiton

## 4.x

### Type hints

All methods recieved type hints for parameters and return types. Check your implementations and update your code. The types
were enforced internally before so there shouldn't be any changes in the usage

### ContainerBuilder

The container builder expected an optional class name before. Now the builder expects an instance of the actual container builder
which can have the class name. If you didn't pass a class name before you don't have to change anything since the default container builder
is created automatically.

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