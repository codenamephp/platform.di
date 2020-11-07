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

namespace de\codenamephp\platform\di\definitionsProvider\factory\byClassname;

use de\codenamephp\platform\di\definitionsProvider\factory\ProviderCouldNotBeCreatedException;
use de\codenamephp\platform\di\definitionsProvider\factory\ProviderDoesNotImplementProviderInterfaceException;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;

/**
 * Interface to create providers by classname
 */
interface iByClassname {

  /**
   * Creates a new definition provider from the given classname. Implementations should make use of the arguments as they see fit (e.g. as constructor
   * arguments) but must keep in mind that they are optional
   *
   * @param string $classname The FQDN of the provider to be created
   * @param mixed ...$arguments Optional list of arguments that can be used to create the provider
   * @return iDefinitionsProvider
   * @throws ProviderCouldNotBeCreatedException
   * @throws ProviderDoesNotImplementProviderInterfaceException
   */
  public function build(string $classname, ...$arguments) : iDefinitionsProvider;
}