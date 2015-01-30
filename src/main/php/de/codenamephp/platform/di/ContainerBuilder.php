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
class ContainerBuilder {

  /**
   * The glob pattern to load the definitions
   *
   * By default, the following order is used:
   *
   * - global.php
   * - *.global.php
   * - local.php
   * - *.local.php
   *
   * @var string
   */
  private $globPaths = array();

  /**
   *
   * @var \DI\ContainerBuilder
   */
  private $containerBuilder = null;

  public function __construct() {
    $this->setContainerBuilder(new \DI\ContainerBuilder(Container::class));
  }

  /**
   *
   * @return array
   */
  public function getGlobPaths() {
    return $this->globPaths;
  }

  /**
   *
   * @param array $globPaths
   * @return self
   */
  public function setGlobPaths(array $globPaths) {
    $this->globPaths = $globPaths;
    return $this;
  }

  public function addGlobPath($globPath) {
    $this->globPaths[] = (string) $globPath;
  }

  public function getContainerBuilder() {
    return $this->containerBuilder;
  }

  public function setContainerBuilder(\DI\ContainerBuilder $containerBuilder) {
    $this->containerBuilder = $containerBuilder;
    return $this;
  }

  public function build() {
    $containerBuilder = $this->getContainerBuilder();

    foreach($this->getGlobPaths() as $globPath) {
      foreach(glob($globPath, GLOB_BRACE) as $definitionFile) {
        $containerBuilder->addDefinitions($definitionFile);
      }
    }

    return $containerBuilder->build();
  }
}