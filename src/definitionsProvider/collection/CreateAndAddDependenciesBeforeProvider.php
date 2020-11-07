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

use de\codenamephp\platform\di\definitionsProvider\dependency\CircularDependencyException;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\factory\byClassname\iByClassname;
use de\codenamephp\platform\di\definitionsProvider\factory\byClassname\SimpleNew;
use de\codenamephp\platform\di\definitionsProvider\factory\byClassname\tByClassname;
use de\codenamephp\platform\di\definitionsProvider\factory\ProviderCouldNotBeCreatedException;
use de\codenamephp\platform\di\definitionsProvider\factory\ProviderDoesNotImplementProviderInterfaceException;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;

/**
 * This collection creates and adds all dependencies of the privder before the provider itself is added. It uses an iByClassname factory to create the
 * provider and keeps an array cache to keep track of added providers and only create providers that havn't been added yet.
 *
 * @psalm-suppress PropertyNotSetInConstructor see https://github.com/vimeo/psalm/issues/4393
 */
final class CreateAndAddDependenciesBeforeProvider implements iCollection {

  use tCollection, tByClassname;

  /**
   * Array of class names of providers that have already been added so they don't have to be created
   *
   * @var string[]
   */
  private array $addedProviderClassnames = [];

  public function __construct(iCollection $providerCollection = null, iByClassname $byClassnameProviderFactory = null) {
    $this->setProviderCollection($providerCollection ?? new SimpleArray());
    $this->setByClassnameProviderFactory($byClassnameProviderFactory ?? new SimpleNew());
  }

  /**
   * @return string[]
   */
  public function getAddedProviderClassnames() : array {
    return $this->addedProviderClassnames;
  }

  /**
   * @param string[] $addedProviderClassnames
   * @return $this
   */
  public function setAddedProviderClassnames(array $addedProviderClassnames) : CreateAndAddDependenciesBeforeProvider {
    $this->addedProviderClassnames = $addedProviderClassnames;
    return $this;
  }

  /**
   * @return iDefinitionsProvider[]
   * @throws CircularDependencyException
   * @throws MissingDependencyException
   */
  public function get() : array {
    return $this->getProviderCollection()->get();
  }

  /**
   * If the given provider implements the iDependsOn interface its dependencies are iterated over. If the classname does not yet exist in the
   * addedProviderClassnames array the dependency is created using the classname with the provider factory and is passed recursivly to the add method.
   *
   * Finally the provider is added to the collection and the classname is added to the alreadyCreatedClassnames array so if the provider appears again in any
   * dependency we can skipt the "create and add" part.
   *
   * @param iDefinitionsProvider $provider
   * @return $this
   * @throws CircularDependencyException
   * @throws MissingDependencyException
   * @throws ProviderCouldNotBeCreatedException
   * @throws ProviderDoesNotImplementProviderInterfaceException
   */
  public function add(iDefinitionsProvider $provider) : iCollection {
    if($provider instanceof iDependsOn) {
      foreach($provider->getDependencies() as $dependencyClassname) {
        if(!in_array($dependencyClassname, $this->getAddedProviderClassnames(), true)) $this->add($this->getByClassnameProviderFactory()->build($dependencyClassname));
      }
    }

    $this->getProviderCollection()->add($provider);
    $this->addedProviderClassnames[] = get_class($provider);

    return $this;
  }

}