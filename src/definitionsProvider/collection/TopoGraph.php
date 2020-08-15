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

use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;
use MJS\TopSort\TopSortInterface;

/**
 * This collection sorts the providers according to their dependencies when they are fetched. The providers are then in the correct order so that providers
 * other providers depends on are before their dependencies.
 *
 * @see https://en.wikipedia.org/wiki/Topological_graph
 */
final class TopoGraph implements iCollection {
  /**
   * @var iDefinitionsProvider[]
   */
  private array $providers = [];

  private TopSortInterface $sort;

  public function __construct(TopSortInterface $sort = null) {
    $this->setSort($sort ?? new StringSort());
  }

  /**
   * @return iDefinitionsProvider[]
   */
  public function getProviders() : array {
    return $this->providers;
  }

  /**
   * @param iDefinitionsProvider[] $providers
   * @return TopoGraph
   */
  public function setProviders(array $providers) : TopoGraph {
    $this->providers = $providers;
    return $this;
  }

  /**
   * @return TopSortInterface
   */
  public function getSort() : TopSortInterface {
    return $this->sort;
  }

  /**
   * @param TopSortInterface $sort
   * @return TopoGraph
   */
  public function setSort(TopSortInterface $sort) : TopoGraph {
    $this->sort = $sort;
    return $this;
  }

  /**
   * Sorts the collected providers by topo sorting the dependency graph and then merging the providers (that have
   * their class names as keys) into
   *
   * @return iDefinitionsProvider[]
   * @throws MissingDependencyException if one or more dependencies are missing
   * @throws \de\codenamephp\platform\di\definitionsProvider\dependency\CircularDependencyException if a circular dependency was detected
   *
   * @since 5.0
   *
   */
  public function get() : array {
    try {
      return array_values(array_filter(array_merge(array_flip($this->getSort()->sort()), $this->getProviders()), static function($provider) {
        return $provider instanceof iDefinitionsProvider;
      }));
    }catch(CircularDependencyException $circularDependencyException) {
      throw new \de\codenamephp\platform\di\definitionsProvider\dependency\CircularDependencyException('Circular dependency detected', 0, $circularDependencyException);
    }catch(ElementNotFoundException $elementNotFoundException) {
      throw new MissingDependencyException('Missing dependency detected', 0, $elementNotFoundException);
    }
  }

  /**
   * Adds the given provider to the collection
   *
   * @param iDefinitionsProvider $provider The provider to add to the collection
   * @return $this
   *
   * @since 5.0
   */
  public function add(iDefinitionsProvider $provider) : iCollection {
    $this->sort->add(get_class($provider), $provider instanceof iDependsOn ? $provider->getDependencies() : []);
    $this->providers[get_class($provider)] = $provider;
    return $this;
  }
}