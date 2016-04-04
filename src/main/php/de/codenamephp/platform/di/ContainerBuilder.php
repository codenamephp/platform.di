<?php
/*
 * Copyright 2015 Bastian Schwarz <bastian@codename-php.de>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @namespace
 */
namespace de\codenamephp\platform\di;

/**
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class ContainerBuilder extends \DI\ContainerBuilder {

  public function __construct($containerClass = Container::class) {
    parent::__construct($containerClass);
  }

  /**
   * Discovers all files found from glob and adds them to the existing definitions by calling self::addDefinitions for each found file
   *
   * @param string $globPath
   * @return self
   */
  public function addGlobPath($globPath) {
    foreach(glob($globPath, GLOB_BRACE) as $definitionFile) {
      $this->addDefinitions($definitionFile);
    }
    return $this;
  }

  /**
   * Adds definitions by a provider class. The provider must implement one of the definitionsProvider\* interfaces and the configuration will be added accordingly to the
   * container builder.
   *
   * @param \de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider $provider
   * @return self
   */
  public function addDefinitionsByProvider(definitionsProvider\iDefintionsProvider $provider) {
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
