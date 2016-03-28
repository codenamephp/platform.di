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

  public function __construct($containerClass = Container::class) {
    parent::__construct($containerClass);
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

  /**
   * Adds a new glob to the glob path array
   *
   * @param string $globPath
   * @return self
   */
  public function addGlobPath($globPath) {
    $this->globPaths[] = (string) $globPath;
    return $this;
  }

  /**
   * Builds the container by adding all files discovered by the glob paths to the container builder and returns the result of the containers build method
   *
   * @return \de\codenamephp\platform\di\iContainer
   */
  public function build() {
    foreach($this->getGlobPaths() as $globPath) {
      foreach(glob($globPath, GLOB_BRACE) as $definitionFile) {
        $this->addDefinitions($definitionFile);
      }
    }

    return parent::build();
  }
}
