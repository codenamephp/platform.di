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

use PHPUnit\Framework\TestCase;

class tContainerTest extends TestCase {

  /**
   *
   *
   * @var tContainer
   */
  private $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = $this->getMockBuilder(tContainer::class)->getMockForTrait();
  }

  public function testSetContainer() : void {
    $container = $this->createMock(iContainer::class);
    $this->sut->setDiContainer($container);

    self::assertSame($container, $this->sut->getDiContainer());
  }

  /**
   * @depends testSetContainer
   */
  public function testGetContainer() : void {
    $container = $this->createMock(iContainer::class);
    $this->sut->setDiContainer($container);

    self::assertSame($container, $this->sut->getDiContainer());
  }
}
