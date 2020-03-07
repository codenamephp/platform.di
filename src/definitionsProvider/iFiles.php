<?php
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

namespace de\codenamephp\platform\di\definitionsProvider;

/**
 * Interface for classes that provide a list of files that contain the defintions. These will be added to the container builder which then loads the definitions
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
interface iFiles extends iDefintionsProvider {

  /**
   * Return the list of files as string array. The files have to be absolute paths and will be added to the container in the sequence they are in the array
   *
   * @return string[] The array of files
   */
  public function getFiles() : array;
}
