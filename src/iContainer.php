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

namespace de\codenamephp\platform\di;

use DI\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Interface that extends the default interop container interface with factory interface from DI and a set method to set values to the container.
 *
 * @since 3.0 \Interop\Container\ContainerInterface was replaced with \Psr\Container\ContainerInterface
 */
interface iContainer extends ContainerInterface, FactoryInterface {
  /**
   * Define an object or a value in the container.
   *
   * @param string $name Entry name
   * @param mixed $value The value to set
   *
   * @return self fluent interface
   *
   * @deprecated since 5.0 you should never set services directly
   * @since 3.0 Type string of $name is now enforced
   */
  public function set(string $name, $value);
}
