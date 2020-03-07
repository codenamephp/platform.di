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

use de\codenamephp\platform\di\definitionsProvider\dependency\handler\iHandler;
use de\codenamephp\platform\di\definitionsProvider\dependency\Wrapper;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
final class ContainerBuilderTest extends TestCase {

  use MockeryPHPUnitIntegration;

  /**
   *
   * @var ContainerBuilder
   */
  private ContainerBuilder $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new ContainerBuilder();
  }

  /**
   *
   *
   * @throws IOException
   */
  protected function tearDown() : void {
    parent::tearDown();

    $fileSystem = new Filesystem();
    $fileSystem->remove(__DIR__ . '/tmp');
  }

  /**
   *
   *
   * @throws InvalidArgumentException
   * @throws RuntimeException
   * @throws definitionsProvider\dependency\MissingDependencyException
   */
  public function testaddDefinitionsByProvider_canAddDefintionsArray_WhenArrayProviderWasGiven() {
    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addDefinitions'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(['some', 'definitions']);
    $this->sut = $containerBuilder;

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iArray {

      public function getDefinitions() : array {
        return ['some', 'definitions'];
      }
    });
  }

  /**
   *
   *
   * @throws InvalidArgumentException
   * @throws RuntimeException
   * @throws definitionsProvider\dependency\MissingDependencyException
   */
  public function testaddDefinitionsByProvider_canAddFiles_WhenFileProviderWasGiven() {
    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addDefinitions'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/global.php');
    $containerBuilder->expects(self::at(1))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/def.global.php');
    $containerBuilder->expects(self::at(2))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/local.php');
    $containerBuilder->expects(self::at(3))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/dev.local.php');
    $containerBuilder->expects(self::at(4))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/test.local.php');
    $this->sut = $containerBuilder;

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iFiles {

      public function getFiles() : array {
        return [
            __DIR__ . '/tmp/definitions/global.php',
            __DIR__ . '/tmp/definitions/def.global.php',
            __DIR__ . '/tmp/definitions/local.php',
            __DIR__ . '/tmp/definitions/dev.local.php',
            __DIR__ . '/tmp/definitions/test.local.php',
        ];
      }
    });
  }

  /**
   *
   *
   * @throws InvalidArgumentException
   * @throws RuntimeException
   * @throws definitionsProvider\dependency\MissingDependencyException
   */
  public function testaddDefinitionsByProvider_canAddGlobPaths_WhenGlobPathProviderWasGiven() {
    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addGlobPath'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addGlobPath')->with('glob1');
    $containerBuilder->expects(self::at(1))->method('addGlobPath')->with('glob2');
    $containerBuilder->expects(self::at(2))->method('addGlobPath')->with('glob3');
    $containerBuilder->expects(self::at(3))->method('addGlobPath')->with('glob4');
    $this->sut = $containerBuilder;

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iGlobPaths {

      public function getGlobPaths() : array {
        return ['glob1', 'glob2', 'glob3', 'glob4'];
      }
    });
  }

  /**
   *
   *
   * @throws InvalidArgumentException
   * @throws RuntimeException
   * @throws definitionsProvider\dependency\MissingDependencyException
   */
  public function testaddDefinitionsByProvider_canCallDependencyHandler_withProvider_whenProviderImplementsiDependencyInterface() {
    $provider = Mockery::mock(implode(', ', [definitionsProvider\iDefintionsProvider::class, definitionsProvider\dependency\iDependency::class]))->shouldIgnoreMissing();

    $dependencyHandler = $this->getMockBuilder(definitionsProvider\dependency\handler\iHandler::class)->getMock();
    $dependencyHandler->expects(self::once())->method('handle')->with($provider);
    $this->sut->setDependencyHandler($dependencyHandler);

    /** @noinspection PhpParamsInspection */
    $this->sut->addDefinitionsByProvider($provider);
  }

  /**
   *
   *
   * @throws InvalidArgumentException
   * @throws \PHPUnit\Framework\Exception
   * @throws RuntimeException
   * @throws definitionsProvider\dependency\MissingDependencyException
   */
  public function testaddDefinitionsByProvider_canCallDependencyHandler_withWrapper_whenProviderImplementsiDependencyInterface() {
    $provider = $this->createMock(iDefintionsProvider::class);

    $dependencyHandler = $this->createMock(iHandler::class);
    $dependencyHandler->expects(self::once())->method('handle')->with(self::callback(function($dependency) use ($provider) {
      return $dependency instanceof Wrapper && $dependency->getDependency() === $provider;
    }));
    $this->sut->setDependencyHandler($dependencyHandler);

    $this->sut->addDefinitionsByProvider($provider);
  }

  /**
   *
   *
   * @throws InvalidArgumentException
   * @throws RuntimeException
   * @throws IOException
   */
  public function testaddGlobPath_CannAddEachFileFromGlob_ToContainerBuilder() {
    $fileSystem = new Filesystem();
    $fileSystem->mkdir(__DIR__ . '/tmp/definitions', 0777);
    $fileSystem->touch([
        __DIR__ . '/tmp/definitions/global.php',
        __DIR__ . '/tmp/definitions/def.global.php',
        __DIR__ . '/tmp/definitions/local.php',
        __DIR__ . '/tmp/definitions/test.local.php',
        __DIR__ . '/tmp/definitions/dev.local.php',
    ]);

    $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->setMethods(['addDefinitions'])->getMock();
    $containerBuilder->expects(self::at(0))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/global.php');
    $containerBuilder->expects(self::at(1))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/def.global.php');
    $containerBuilder->expects(self::at(2))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/local.php');
    $containerBuilder->expects(self::at(3))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/dev.local.php');
    $containerBuilder->expects(self::at(4))->method('addDefinitions')->with(__DIR__ . '/tmp/definitions/test.local.php');
    $this->sut = $containerBuilder;

    $this->sut->addGlobPath(__DIR__ . '/tmp/definitions/{{,*.}global,{,*.}local}.php');
  }
}
