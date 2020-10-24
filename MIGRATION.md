# Migraiton

## 5.x

The dependency system was reworked so it is not part of the container builder anymore since this violates the SRP and makes
maintaining the builder harder than it needs to be. The system also was simplified and some classes and interface that were never
used were removed.

Check your code if you have used/extended any classes or interface below.

As a general rule:
- Glob path files should be replaced with iArray provider
- iCoversDependencies was hard to understand. Just use iDependsOn
- Use iCollection instead of iHandler
- Use the example code for one of the dependency collections from the readme

### API
#### Removed
- `\de\codenamephp\platform\di\iContainer::set` without replacement
- `\de\codenamephp\platform\di\ContainerBuilder::getDependencyHandler` and `\de\codenamephp\platform\di\ContainerBuilder::setDependencyHandler` without replacement
- `\de\codenamephp\platform\di\definitionsProvider\dependency\handler\iHandler` which is replaced by the collections
- `de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies` without replacement
- `de\codenamephp\platform\di\definitionsProvider\dependency\Wrapper` without replacement
- Removed everything glob path related
  - `\de\codenamephp\platform\di\definitionsProvider\iGlobPaths`
  - `\de\codenamephp\platform\di\iContainerBuilder::addGlobPath`
  - `\de\codenamephp\platform\di\ContainerBuilder::addGlobPath`
#### Changed
- `de\codenamephp\platform\di\definitionsProvider\dependency\handler` is now `\de\codenamephp\platform\di\definitionsProvider\collection\ClassNamesInArray` and 
    implements the new `\de\codenamephp\platform\di\definitionsProvider\collection\iCollection` interface
    
## 4.x

### Type hints

All methods recieved type hints for parameters and return types. Check your implementations and update your code. The types
were enforced internally before so there shouldn't be any changes in the usage.

### ContainerBuilder

The container builder expected an optional class name before. Now the builder expects an instance of the actual container builder
which can have the class name. If you didn't pass a class name before you don't have to change anything since the default container builder
is being created automatically.

The `addDefinitions` method was part of the original container which is now a dependency. Adding definitions this way is discouraged
since they tend to pile up. Instead, just add them using a DefinitionsProvider class. 
Of course, I can't stop you from adding them using an anonymous class or adding them to the PHP-Di container directly.

## 3.x

### PHP-DI

The biggist change is the support of php-di 6 and the dropped support of php-di 5. The migration is somwhat huge
depending on the number of definitions. See http://php-di.org/doc/migration/6.0.html for details

### API

- All occurances of `Interop\Container\ContainerInterface` have been replaced with `\Psr\Container\ContainerInterface` so updated your usages accordingly
- The types of `\de\codenamephp\platform\di\Container::__construct` have changed so if you instantiate the container yourself (you shouldn't, that's what the 
  builder is for) you have to update the type of the first and 3rd parameter
- All parameters of `\de\codenamephp\platform\di\Container::__construct` are now optoinal so if you just passed them because you had to, remove them

### PHP

PHP7 is now required for php-di 6 and type hints
