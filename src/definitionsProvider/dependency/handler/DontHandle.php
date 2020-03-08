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
 */

namespace de\codenamephp\platform\di\definitionsProvider\dependency\handler;

use de\codenamephp\platform\di\definitionsProvider\dependency\iDependency;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;

/**
 * Simple handler that actually doesn't do any dependency checking and can be used to disable this feature.
 */
final class DontHandle implements iHandler {
  /**
   * This method doesn't do anything and just returns so no dependencies are checked and the feature is disabled.
   *
   * @param iDependency $provider THe provider whose dependencies are handled
   *
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing
   */
  public function handle(iDependency $provider) : void {
  }
}
