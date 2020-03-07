<?php declare(strict_types=1);
/**
 * Copyright 2020 Bastian Schwarz <bastian@codename-php.de>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace de\codenamephp\platform\di;

use de\codenamephp\platform\di\definitionsProvider\dependency\handler\iHandler;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\dependency\Wrapper;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;
use InvalidArgumentException;

/**
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class ContainerBuilder extends \DI\ContainerBuilder {

  /**
   * Handles the dependencies of providers
   *
   * @var definitionsProvider\dependency\handler\iHandler
   */
  private iHandler $dependencyHandler;

  /**
   * Calls the parent constructor with the given class name and sets a new instance of definitionsProvider\dependency\handler\DontHandle as dependencyHandler
   * so this new feature is disabled by default.
   *
   * @param string $containerClass The class name of the container that will be created
   */
  public function __construct($containerClass = Container::class) {
    parent::__construct($containerClass);
    $this->sourceCacheNamespace = '';
    $this->setDependencyHandler(new definitionsProvider\dependency\handler\DontHandle());
  }

  /**
   *
   * @return definitionsProvider\dependency\handler\iHandler
   */
  public function getDependencyHandler() : iHandler {
    return $this->dependencyHandler;
  }

  /**
   *
   * @param iHandler $dependencyHandler
   *
   * @return $this
   */
  public function setDependencyHandler(definitionsProvider\dependency\handler\iHandler $dependencyHandler) : self {
    $this->dependencyHandler = $dependencyHandler;
    return $this;
  }

  /**
   * Adds definitions by a provider class. The provider must implement one of the definitionsProvider\* interfaces and the configuration will be added
   * accordingly to the container builder.
   *
   * Also the dependencies are checked here using the iHandler. If the provider implements the iDependency interface, it is used directly for the dependency
   * check. If not, it is wrapper in the Wrapper dependency which is then used.
   *
   * @param iDefintionsProvider $provider The provider whose definitions will be added, depending on the implemented interfaces
   * @return self
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing (from dependencyHandler)
   * @throws InvalidArgumentException
   */
  public function addDefinitionsByProvider(definitionsProvider\iDefintionsProvider $provider) : self {
    if($provider instanceof definitionsProvider\dependency\iDependency) {
      $dependency = $provider;
    }else {
      $dependency = new Wrapper($provider);
    }
    $this->getDependencyHandler()->handle($dependency);

    if($provider instanceof definitionsProvider\iFiles) {
      foreach($provider->getFiles() as $file) {
        $this->addDefinitions($file);
      }
    }

    if($provider instanceof definitionsProvider\iArray) {
      $this->addDefinitions($provider->getDefinitions());
    }

    if($provider instanceof definitionsProvider\iGlobPaths) {
      foreach($provider->getGlobPaths() as $globPath) {
        $this->addGlobPath($globPath);
      }
    }
    return $this;
  }

  /**
   * Discovers all files found from glob and adds them to the existing definitions by calling self::addDefinitions for each found file
   *
   * @param string $globPath
   *
   * @return self
   * @throws InvalidArgumentException
   */
  public function addGlobPath($globPath) : self {
    foreach(glob($globPath, GLOB_BRACE) as $definitionFile) {
      $this->addDefinitions($definitionFile);
    }
    return $this;
  }
}
