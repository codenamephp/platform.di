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

namespace de\codenamephp\platform\di\definitionsProvider\dependency;

/**
 * Dependency wrapper that can be used to handle providers that do net yet implement the iDependency interface
 */
final class Wrapper implements iCoversDependencies {

  /**
   * The actual dependency that is wrapped
   *
   * @var object
   */
  private object $dependency;

  /**
   *
   * @param object $dependency The actual dependency that is wrapped
   */
  public function __construct(object $dependency) {
    $this->setDependency($dependency);
  }

  /**
   * @return object
   */
  public function getDependency() : object {
    return $this->dependency;
  }

  /**
   * @param object $dependency
   * @return Wrapper
   */
  public function setDependency(object $dependency) : self {
    $this->dependency = $dependency;
    return $this;
  }

  /**
   * Returns an array of provider class names that this provider covers
   *
   * @return string[] Array of provider class names that are covered by this provider
   */
  public function getCoveredDependencies() : array {
    return [get_class($this->getDependency())];
  }
}