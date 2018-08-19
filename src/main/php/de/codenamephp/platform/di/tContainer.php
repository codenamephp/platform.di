<?php
/**
 * Copyright 2018 Bastian Schwarz <bastian@codename-php.de>.
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

/**
 * Simple trait for the iContainer interface that contains the member and getter/setter
 */
trait tContainer {

  /**
   * The di container
   *
   * @var iContainer
   */
  private $diContainer = null;

  /**
   * @return iContainer
   */
  public function getDiContainer(): iContainer {
    return $this->diContainer;
  }

  /**
   * @param iContainer $diContainer
   *
   * @return self
   */
  public function setDiContainer(iContainer $diContainer): self {
    $this->diContainer = $diContainer;
    return $this;
  }
}