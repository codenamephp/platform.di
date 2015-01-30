<?php
/*
 * Copyright 2015 Bastian Schwarz <bastian@codename-php.de>.
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
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class ContainerBuilderTest extends TestCase {

  /**
   *
   * @var ContainerBuilder
   */
  private $sut = null;

  protected function setUp() {
    parent::setUp();

    \org\bovigo\vfs\vfsStream::setup();

    $containerBuilder = $this->getMockBuilder(\DI\ContainerBuilder::class)->disableOriginalConstructor()->getMock();

    $this->sut = new ContainerBuilder();
    $this->sut->setContainerBuilder($containerBuilder);
  }

  protected function tearDown() {
    parent::tearDown();

    $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
    $fileSystem->remove(__DIR__ . '/tmp');
  }

  public function testbuild_CanCallbuildOnContainerBulder_AndReturnItsResult() {
    $containerBuilder = $this->getMockBuilder(\DI\ContainerBuilder::class)->disableOriginalConstructor()->getMock();
    $containerBuilder->expects(self::once())->method('build')->willReturn('some result');
    $this->sut->setContainerBuilder($containerBuilder);

    self::assertEquals('some result', $this->sut->build());
  }

  public function testbuild_canSetCustomContainerClassName() {
    $this->sut = new ContainerBuilder();
    self::assertSame(Container::class, $this->reflect($this->sut->getContainerBuilder())->containerClass);
  }

  public function testbuild_CannAddEachFileFromGlob_ToContainerBuilder() {
    $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
    $fileSystem->mkdir(__DIR__ . '/tmp/definitions', 0777);
    $fileSystem->touch(array(
        __DIR__ . '/tmp/definitions/global.php',
        __DIR__ . '/tmp/definitions/def.global.php',
        __DIR__ . '/tmp/definitions/local.php',
        __DIR__ . '/tmp/definitions/test.local.php',
        __DIR__ . '/tmp/definitions/dev.local.php'
    ));

    $containerBuilder = $this->getMockBuilder(\DI\ContainerBuilder::class)->disableOriginalConstructor()->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/global.php');
    $containerBuilder->expects(self::at(1))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/def.global.php');
    $containerBuilder->expects(self::at(2))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/local.php');
    $containerBuilder->expects(self::at(3))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/dev.local.php');
    $containerBuilder->expects(self::at(4))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/test.local.php');
    $this->sut->setContainerBuilder($containerBuilder);

    $this->sut->addGlobPath(__DIR__ . '/tmp/definitions/{{,*.}global,{,*.}local}.php');

    $this->sut->build();
  }
}