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
 */

namespace de\codenamephp\platform\di;

use de\codenamephp\platform\di\definitionsProvider\dependency\handler\iHandler;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\dependency\Wrapper;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;
use de\codenamephp\platform\di\definitionsProvider\iMetaProvider;
use InvalidArgumentException;

final class ContainerBuilder implements iContainerBuilder {
  /**
   * The actual container builder that will be used to create the container and where the definitions are added to.
   *
   * @var \DI\ContainerBuilder
   */
  private \DI\ContainerBuilder $containerBuilder;

  /**
   * Handles the dependencies of providers.
   *
   * @var definitionsProvider\dependency\handler\iHandler
   */
  private iHandler $dependencyHandler;

  /**
   * Sets the container builder or creates one if null was given. Also sets an instance of
   * \de\codenamephp\platform\di\definitionsProvider\dependency\handler\DontHandle as default dependency handler.
   *
   * @param null|\DI\ContainerBuilder $containerBuilder The actual container builder that will be used to create the container and where the definitions are added to
   */
  public function __construct(\DI\ContainerBuilder $containerBuilder = null) {
    $this->setContainerBuilder($containerBuilder ?? new \DI\ContainerBuilder(Container::class));
    $this->setDependencyHandler(new definitionsProvider\dependency\handler\DontHandle());
  }

  public function getContainerBuilder() : \DI\ContainerBuilder {
    return $this->containerBuilder;
  }

  public function setContainerBuilder(\DI\ContainerBuilder $containerBuilder) : iContainerBuilder {
    $this->containerBuilder = $containerBuilder;
    return $this;
  }

  /**
   * @return definitionsProvider\dependency\handler\iHandler
   */
  public function getDependencyHandler() : iHandler {
    return $this->dependencyHandler;
  }

  /**
   * @param iHandler $dependencyHandler
   */
  public function setDependencyHandler(definitionsProvider\dependency\handler\iHandler $dependencyHandler) : iContainerBuilder {
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
   *
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing (from dependencyHandler)
   * @throws InvalidArgumentException
   */
  public function addDefinitionsByProvider(definitionsProvider\iDefintionsProvider $provider) : iContainerBuilder {
    $this->getDependencyHandler()->handle($provider instanceof definitionsProvider\dependency\iDependency ? $provider : new Wrapper($provider));

    if($provider instanceof definitionsProvider\iFiles) {
      foreach($provider->getFiles() as $file) {
        $this->getContainerBuilder()->addDefinitions($file);
      }
    }

    if($provider instanceof definitionsProvider\iArray) {
      $this->getContainerBuilder()->addDefinitions($provider->getDefinitions());
    }

    if($provider instanceof definitionsProvider\iGlobPaths) {
      foreach($provider->getGlobPaths() as $globPath) {
        $this->addGlobPath($globPath);
      }
    }

    if($provider instanceof iMetaProvider) {
      foreach($provider->getProviders() as $metaProvider) {
        $this->addDefinitionsByProvider($metaProvider);
      }
    }
    return $this;
  }

  /**
   * Discovers all files found from glob and adds them to the wrapped container builder.
   *
   * @param string $globPath A glob path that will be used to discover definition files
   *
   * @throws InvalidArgumentException
   */
  public function addGlobPath($globPath) : iContainerBuilder {
    foreach(glob($globPath, GLOB_BRACE) as $definitionFile) {
      $this->getContainerBuilder()->addDefinitions($definitionFile);
    }
    return $this;
  }

  /**
   * Uses the wrapped container builder to build the container (duh!) and checks the type for the iContainer interface. If the built container
   * is not of type iContainer an exception is thrown.
   *
   * @throws \Exception if the build container was not of type iContainer
   */
  public function build() : iContainer {
    $container = $this->getContainerBuilder()->build();
    if(!$container instanceof iContainer) {
      throw new Exception(sprintf('Built container is not of type %s', iContainer::class));
    }
    return $container;
  }
}
