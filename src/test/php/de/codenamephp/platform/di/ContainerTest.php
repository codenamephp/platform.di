<?php
/*
 * Copyright 2016 Bastian Schwarz <bastian@codename-php.de>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @namespace
 */
namespace de\codenamephp\platform\di;

/**
 *
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class ContainerTest extends \de\codenamephp\platform\test\TestCase {

  /**
   *
   * @var Container
   */
  private $sut = null;

  protected function setUp() {
    parent::setUp();

    $proxyFactory = $this->getMockBuilder(\DI\Proxy\ProxyFactory::class)->disableOriginalConstructor()->getMock();

    $this->sut = new Container($this->createMock(\DI\Definition\Source\DefinitionSource::class), $proxyFactory);
  }

  public function testset_canReturnSelf_ToImplementFluentInterface() {
    self::assertSame($this->sut, $this->sut->set('test', 'test'));
  }

  public function testconstruct_canAddDefinitionsForContainer_andInterface() {
    self::assertInstanceOf(Container::class, $this->sut->get(Container::class));
    self::assertInstanceOf(Container::class, $this->sut->get(iContainer::class));
  }
}