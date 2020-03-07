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

namespace de\codenamephp\platform\di\definitionsProvider\dependency\handler;

use de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependency;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;

/**
 * Collects and compares the class names in an array. No checks are performed for parent classes or circular dependencies, jsut the information returned from the providers is used.
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
final class ClassNamesInArray implements iHandler {

  /**
   * Holds the already collected dependency class names
   *
   * @var string[]
   */
  private array $collectedDependencies = [];

  /**
   *
   * @return string[]
   */
  public function getCollectedDependencies() : array {
    return $this->collectedDependencies;
  }

  /**
   *
   * @param string[] $collectedDependencies
   * @return $this
   */
  public function setCollectedDependencies(array $collectedDependencies) : self {
    $this->collectedDependencies = $collectedDependencies;
    return $this;
  }

  /**
   * Adds the given dependencies to the collectedDependencies without duplicating values and while maintining serial numerical index
   *
   * @param string[] $dependenciesToAdd
   * @return $this
   */
  public function addDependencies(array $dependenciesToAdd) : self {
    $this->setCollectedDependencies(array_merge(array_unique(array_merge($this->getCollectedDependencies(), $dependenciesToAdd))));
    return $this;
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
   * @param iDependency $provider THe provider whose dependencies are handled
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing
   */
  public function handle(iDependency $provider) : void {
    if($provider instanceof iDependsOn && count(array_diff($provider->getDependencies(), $this->getCollectedDependencies())) > 0) {
      throw new MissingDependencyException(sprintf('The provider "%s" is missing dependencies. Plaese add them to the container before adding this provider. Missing dependencies: '
          . "[\n\t\t%s\n\t]", get_class($provider), implode("\n\t\t", array_diff($provider->getDependencies(), $this->getCollectedDependencies()))));
    }
    if($provider instanceof iCoversDependencies) {
      $this->addDependencies($provider->getCoveredDependencies());
    }else {
      $this->addDependencies([get_class($provider)]);
    }
  }
}
