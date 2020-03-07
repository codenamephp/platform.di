# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [4.1.0] - 2020-03-08 Added MetaProvider interface
### Added
- Added new `\de\codenamephp\platform\di\definitionsProvider\iMetaProvider` interface to add multiple providers with a single class

## [4.0.0] - 2020-03-07 Strict typing, composition and QA
### Added
- Added psalm as QA tool
- Added phive to install QA tools
- Added `\de\codenamephp\platform\di\iContainerBuilder` interface

### Changed
- Switched from Jenkins (which wasn't operational in a few years) to travis
- Updated PHPComp
- Added strict type declaration to all files
- Added type hints and return type hints to all methods
- ContainerBuilder was changed from inheritance to composition
- Made classes final

## [3.0.0] - 2018-08-19 Update to php-di 6
### Changed
- Dependency to php-di is now ^6 which requires PHP7 @bastianschwarz
- Replaced `Interop\Container\ContainerInterface` with `\Psr\Container\ContainerInterface` in `\de\codenamephp\platform\di\iContainer` @bastianschwarz
- Changed constructor of `\de\codenamephp\platform\di\Container` to expect an instance of `\DI\Definition\Source\MutableDefinitionSource` and 
  `\Psr\Container\ContainerInterface` as the parent constructor was changed @bastianschwarz
- All parameters of `\de\codenamephp\platform\di\Container::__construct` are now optional same as the parent constructor @bastianschwarz
- The type of the first parameter $name of `\de\codenamephp\platform\di\Container::set` is now enforced via type hint @bastianschwarz
  
## [2.2.0] - 2018-08-19 
### Added
- Added trait for the container @bastianschwarz
- Added `\de\codenamephp\platform\di\definitionsProvider\dependency\Wrapper` and enabled dependency check for classes that do not implement
  the `\de\codenamephp\platform\di\definitionsProvider\dependency\iDependency` interface @bastianschwarz
  
## [2.1.0] - 2016-10-22 - Provider dependencies
### Added
- Dependecy feature @bastianschwarz

## [2.0.1] - 2016-05-26 - PHP version requirement downgrade

### Changed
- Downgrade from PHP7 to PHP5.6 so infomax can use it @bastianschwarz

## [2.0.0] - 2016-04-04 - GlobPaths are no overwritable

### Changed
- Files from glob path should be added directly, not just on build @bastianschwarz - codenamephp/build#3

## [1.1.0] - 2016-03-29 - Added provider config

### Added
- DefinitionsProvider feature @bastianschwarz

### Changed
- Requieres PHP7

## [1.0.0] - 2016-03-01 - Initial release

Clone of [0.0.2]

## [0.0.2] - 2015-01-31 New release with php-di 5.0@dev

###Changed
- Updated to php-di 5.0 for new features like value concatination

## [0.0.1] - 2015-01-30 Initial pre-release

Setup of repo, readme, basic funcitons