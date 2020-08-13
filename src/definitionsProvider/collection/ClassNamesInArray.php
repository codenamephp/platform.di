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

namespace de\codenamephp\platform\di\definitionsProvider\collection;

use de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependency;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\dependency\Wrapper;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

/**
 * Collects and compares the class names in an array. No checks are performed for parent classes or circular dependencies, jsut the information returned from the providers is used.
 *
 * @since 5.0
 */
final class ClassNamesInArray implements iCollection {

  /**
   * Holds the already collected dependency class names.
   *
   * @var iCollection
   */
  private iCollection $collection;

  /**
   * Holds the class names of dependencies that were added and where new dependencies are checked against
   *
   * @var string[]
   */
  private array $collectedDependencies = [];

  /**
   * @param iCollection $collection
   */
  public function __construct(iCollection $collection = null) {
    $this->collection = $collection ?? new SimpleArray();
  }

  /**
   * @return iCollection
   *
   * @since 5.0
   */
  public function getCollection() : iCollection {
    return $this->collection;
  }

  /**
   * @param iCollection $collection
   *
   * @return $this
   *
   * @since 5.0
   */
  public function setCollection(iCollection $collection) : self {
    $this->collection = $collection;
    return $this;
  }

  /**
   * @return string[]
   *
   * @since 5.0
   */
  public function getCollectedDependencies() : array {
    return $this->collectedDependencies;
  }

  /**
   * @param string[] $collectedDependencies
   * @return ClassNamesInArray
   *
   * @since 5.0
   */
  public function setCollectedDependencies(array $collectedDependencies) : ClassNamesInArray {
    $this->collectedDependencies = $collectedDependencies;
    return $this;
  }

  /**
   * Gets the collection of providers
   *
   * @return iDefintionsProvider[]
   *
   * @since 5.0
   */
  public function get() : array {
    return $this->getCollection()->get();
  }

  /**
   * First checks if the provider implements the \de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn interface. If so, the values of getDependencies() are checked
   * against the collectedDependencies array. If they are not found, a \de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException is thrown.
   * The check is not stopped on first failure, so the Exception can contain all missing dependencies, which makes debugging and adding missing dependencies more comfortable.
   *
   * If the dependencies check out, the provider is tested for the \de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies interface. If the provider
   * implements this interface, the class from getCoveredDependencies() (and only those) are added to the collectedDependencies array.
   * If the provider does not implement the interface, just the class name of the provider is added. This way, providers that only cover their own dependency (which probably
   * are most of them) don't need to implement an additional interface (and therefore an additional method).
   *
   * @param iDefintionsProvider $provider The provider to add to the collection
   * @return $this
   *
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing
   * @since 5.0
   *
   */
  public function add(iDefintionsProvider $provider) : iCollection {
    $dependency = $provider instanceof iDependency ? $provider : new Wrapper($provider);

    if($dependency instanceof iDependsOn && count(array_diff($dependency->getDependencies(), $this->getCollectedDependencies())) > 0) {
      throw new MissingDependencyException(sprintf(
          <<<EXCEPTION
          The provider "%s" is missing dependencies. Plaese add them to the container before adding this provider. Missing dependencies:
          [
            %s
          ]
          EXCEPTION, get_class($provider), implode("\n\t", array_diff($dependency->getDependencies(), $this->getCollectedDependencies()))));
    }
    if($dependency instanceof iCoversDependencies) {
      $this->addDependencies($dependency->getCoveredDependencies());
    }else {
      $this->addDependencies([get_class($dependency)]);
    }

    $this->getCollection()->add($provider);
    return $this;
  }

  /**
   * Adds the given dependencies to the collectedDependencies without duplicating values and while maintining serial numerical index.
   *
   * @param string[] $dependenciesToAdd
   *
   * @return $this
   *
   * @since 5.0
   */
  public function addDependencies(array $dependenciesToAdd) : self {
    return $this->setCollectedDependencies(array_values(array_unique(array_merge($this->getCollectedDependencies(), $dependenciesToAdd))));
  }
}
