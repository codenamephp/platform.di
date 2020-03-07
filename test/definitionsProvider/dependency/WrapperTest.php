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

namespace de\codenamephp\platform\di\definitionsProvider\dependency;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;

class WrapperTest extends TestCase {

  /**
   *
   *
   * @var Wrapper
   */
  private Wrapper $sut;

  /**
   *
   *
   * @throws \InvalidArgumentException
   */
  protected function setUp() : void {
    parent::setUp();

    $this->sut = new Wrapper(new stdClass());
  }

  /**
   *
   *
   * @throws ExpectationFailedException
   * @throws InvalidArgumentException
   */
  public function testGetCoveredDependencies() {
    self::assertSame([stdClass::class], $this->sut->getCoveredDependencies());
  }

  /**
   *
   *
   * @throws \InvalidArgumentException
   * @throws ExpectationFailedException
   * @throws InvalidArgumentException
   */
  public function test__construct() {
    $dependency = new stdClass();

    $sut = new Wrapper($dependency);

    self::assertSame($dependency, $sut->getDependency());
  }
}
