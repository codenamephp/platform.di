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
class ContainerBuilderTest extends \de\codenamephp\platform\test\TestCase {

  /**
   *
   * @var ContainerBuilder
   */
  private $sut = null;

  protected function setUp() {
    parent::setUp();

    $this->sut = new ContainerBuilder();
  }

  protected function tearDown() {
    parent::tearDown();

    $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
    $fileSystem->remove(__DIR__ . '/tmp');
  }

  public function testconstruct_canSetCustomContainerClassName() {
    $this->sut = new ContainerBuilder();

    self::assertAttributeEquals(Container::class, 'containerClass', $this->sut);
  }

  public function testaddGlobPath_CannAddEachFileFromGlob_ToContainerBuilder() {
    $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
    $fileSystem->mkdir(__DIR__ . '/tmp/definitions', 0777);
    $fileSystem->touch(array(
      __DIR__ . '/tmp/definitions/global.php',
      __DIR__ . '/tmp/definitions/def.global.php',
      __DIR__ . '/tmp/definitions/local.php',
      __DIR__ . '/tmp/definitions/test.local.php',
      __DIR__ . '/tmp/definitions/dev.local.php'
    ));

    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addDefinitions'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/global.php');
    $containerBuilder->expects(self::at(1))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/def.global.php');
    $containerBuilder->expects(self::at(2))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/local.php');
    $containerBuilder->expects(self::at(3))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/dev.local.php');
    $containerBuilder->expects(self::at(4))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/test.local.php');
    $this->sut = $containerBuilder;

    $this->sut->addGlobPath(__DIR__ . '/tmp/definitions/{{,*.}global,{,*.}local}.php');
  }

  public function testaddDefinitionsByProvider_canAddFiles_WhenFileProviderWasGiven() {
    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addDefinitions'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/global.php');
    $containerBuilder->expects(self::at(1))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/def.global.php');
    $containerBuilder->expects(self::at(2))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/local.php');
    $containerBuilder->expects(self::at(3))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/dev.local.php');
    $containerBuilder->expects(self::at(4))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/test.local.php');
    $this->sut = $containerBuilder;

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iFiles {

      public
          function getFiles() {
        return [
          __DIR__ . '/tmp/definitions/global.php',
          __DIR__ . '/tmp/definitions/def.global.php',
          __DIR__ . '/tmp/definitions/local.php',
          __DIR__ . '/tmp/definitions/dev.local.php',
          __DIR__ . '/tmp/definitions/test.local.php'
        ];
      }
    });
  }

  public function testaddDefinitionsByProvider_canAddDefintionsArray_WhenArrayProviderWasGiven() {
    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addDefinitions'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(['some', 'definitions']);
    $this->sut = $containerBuilder;

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iArray {

      public
          function getDefinitions() {
        return ['some', 'definitions'];
      }
    });
  }

  public function testaddDefinitionsByProvider_canAddGlobPaths_WhenGlobPathProviderWasGiven() {
    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addGlobPath'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addGlobPath')->with('glob1');
    $containerBuilder->expects(self::at(1))->method('addGlobPath')->with('glob2');
    $containerBuilder->expects(self::at(2))->method('addGlobPath')->with('glob3');
    $containerBuilder->expects(self::at(3))->method('addGlobPath')->with('glob4');
    $this->sut = $containerBuilder;

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iGlobPaths {

      public
          function getGlobPaths() {
        return ['glob1', 'glob2', 'glob3', 'glob4'];
      }
    });
  }
}
