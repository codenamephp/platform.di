<?php
/**
 * Copyright 2018 Bastian Schwarz <bastian@codename-php.de>.
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
  private $dependencyHandler = null;

  /**
   * Calls the parent constructor with the given class name and sets a new instance of definitionsProvider\dependency\handler\DontHandle as dependencyHandler so this new feature
   * is disabled by default.
   *
   * @param string $containerClass The class name of the container that will be created
   */
  public function __construct($containerClass = Container::class) {
    parent::__construct($containerClass);
    $this->setDependencyHandler(new definitionsProvider\dependency\handler\DontHandle());
  }

  /**
   *
   * @return definitionsProvider\dependency\handler\iHandler
   */
  public function getDependencyHandler() {
    return $this->dependencyHandler;
  }

  /**
   *
   * @param \de\codenamephp\platform\di\definitionsProvider\dependency\handler\iHandler $dependencyHandler
   * @return $this
   */
  public function setDependencyHandler(definitionsProvider\dependency\handler\iHandler $dependencyHandler) {
    $this->dependencyHandler = $dependencyHandler;
    return $this;
  }

  /**
   * Discovers all files found from glob and adds them to the existing definitions by calling self::addDefinitions for each found file
   *
   * @param string $globPath
   *
   * @return self
   * @throws \InvalidArgumentException
   */
  public function addGlobPath($globPath) {
    foreach(glob($globPath, GLOB_BRACE) as $definitionFile) {
      $this->addDefinitions($definitionFile);
    }
    return $this;
  }

  /**
   * Adds definitions by a provider class. The provider must implement one of the definitionsProvider\* interfaces and the configuration will be added
   * accordingly to the container builder.
   *
   * @param \de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider $provider The provider whose definitions will be added, depending on the
   *   implemented interfaces
   *
   * @return self
   * @throws \de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException if a dependency that the given provider relies on is missing
   *   (from dependencyHandler)
   * @throws \InvalidArgumentException
   */
  public function addDefinitionsByProvider(definitionsProvider\iDefintionsProvider $provider) {
    if($provider instanceof definitionsProvider\dependency\iDependency) {
      $this->getDependencyHandler()->handle($provider);
    }

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
}
