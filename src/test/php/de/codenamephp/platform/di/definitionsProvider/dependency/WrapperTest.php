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

namespace de\codenamephp\platform\di\definitionsProvider\dependency;

use PHPUnit\Framework\TestCase;

class WrapperTest extends TestCase {
  /**
   *
   *
   * @var Wrapper
   */
  private $sut = null;

  /**
   *
   *
   * @throws \InvalidArgumentException
   */
  protected function setUp() {
    parent::setUp();

    $this->sut = new Wrapper(new \stdClass());
  }

  /**
   *
   *
   * @throws \PHPUnit\Framework\ExpectationFailedException
   * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
   */
  public function testGetCoveredDependencies() {
    self::assertSame([\stdClass::class], $this->sut->getCoveredDependencies());
  }

  /**
   *
   *
   * @throws \InvalidArgumentException
   * @throws \PHPUnit\Framework\ExpectationFailedException
   * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
   */
  public function test__construct() {
    $dependency = new \stdClass();

    $sut = new Wrapper($dependency);

    self::assertAttributeSame($dependency, 'dependency', $sut);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function test__construct_canThrowInvalidArgumentException_whenDependencyIsNotAnObject() {
    /** @noinspection PhpParamsInspection */
    new Wrapper('not an object');
  }
}
