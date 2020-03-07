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

namespace de\codenamephp\platform\di;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class tContainerTest extends TestCase {

  /**
   *
   *
   * @var tContainer
   */
  private $sut = null;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = $this->getMockBuilder(tContainer::class)->getMockForTrait();
  }

  /**
   *
   *
   * @throws \InvalidArgumentException
   * @throws \PHPUnit\Framework\Exception
   * @throws ExpectationFailedException
   * @throws InvalidArgumentException
   */
  public function testSetContainer() {
    $container = $this->createMock(iContainer::class);
    $this->sut->setDiContainer($container);

    self::assertSame($container, $this->sut->getDiContainer());
  }

  /**
   * @depends testSetContainer
   *
   * @throws \InvalidArgumentException
   * @throws \PHPUnit\Framework\Exception
   * @throws ExpectationFailedException
   * @throws InvalidArgumentException
   */
  public function testGetContainer() {
    $container = $this->createMock(iContainer::class);
    $this->sut->setDiContainer($container);

    self::assertSame($container, $this->sut->getDiContainer());
  }
}
