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

use de\codenamephp\platform\di\definitionsProvider\dependency\iDependency;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;

/**
 * Interface for handlers that handle the dependencies of definition providers
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
interface iHandler {

  /**
   * This method should check the definitions the provider relies on against the already collected definitions. If a definition is missing, a
   * \de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException must be thrown.
   *
   * If all dependencies check out, this method should add all definitions that are covered by the provider to the already collected definitions (e.g. those from
   * getCoveredDependencies() if the provider implements the \de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies interface or just the class name
   * otherwise).
   *
   * @param iDependency $provider THe provider whose dependencies are handled
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing
   */
  public function handle(iDependency $provider) : void;
}
