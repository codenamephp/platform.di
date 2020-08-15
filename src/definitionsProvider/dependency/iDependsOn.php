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

use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

/**
 * Interface that can be added to definitions providers that depend on other definition provider that already have to be added to the container builder.
 */
interface iDependsOn extends iDefintionsProvider {

  /**
   * Gets an array of class names that this provider depends on. These providers need to be added to the container before this provider can be added.
   *
   * This way, the provider can be sure that it can extend definitions.
   *
   * @return string[] The class names of the providers that this provider depends on
   */
  public function getDependencies() : array;
}
