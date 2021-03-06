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

use de\codenamephp\platform\di\definitionsProvider\iArray;
use de\codenamephp\platform\di\definitionsProvider\iFiles;
use de\codenamephp\platform\di\definitionsProvider\iMetaProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
final class ContainerBuilderTest extends TestCase {
  private iContainerBuilder $sut;

  protected function setUp() : void {
    parent::setUp();

    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);

    $this->sut = new ContainerBuilder($containerBuilder);
  }

  public function test__construct_canCreateDefaultContainer_ifNoContainerIsGiven() : void {
    self::assertEquals(new \DI\ContainerBuilder(Container::class), (new ContainerBuilder())->getContainerBuilder());
  }

  public function test__construct_canSetGivenContainerBuilder() : void {
    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);

    self::assertSame($containerBuilder, (new ContainerBuilder($containerBuilder))->getContainerBuilder());
  }

  public function testaddDefinitionsByProvider_canAddDefinitionsArray_WhenArrayProviderWasGiven() : void {
    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);
    $containerBuilder->expects(self::once())->method('addDefinitions')->with(['some', 'definitions']);
    $this->sut->setContainerBuilder($containerBuilder);

    $this->sut->addDefinitionsByProvider(new class() implements definitionsProvider\iArray {
      public function getDefinitions() : array {
        return ['some', 'definitions'];
      }
    });
  }

  public function testaddDefinitionsByProvider_canAddFiles_WhenFileProviderWasGiven() : void {
    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);
    $containerBuilder
      ->expects(self::exactly(5))
      ->method('addDefinitions')
      ->withConsecutive(
        [__DIR__ . '/tmp/definitions/global.php'],
        [__DIR__ . '/tmp/definitions/def.global.php'],
        [__DIR__ . '/tmp/definitions/local.php'],
        [__DIR__ . '/tmp/definitions/dev.local.php'],
        [__DIR__ . '/tmp/definitions/test.local.php']
      )
    ;
    $this->sut->setContainerBuilder($containerBuilder);

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

  public function testaddMetaProvider_canRecurseAndAddProviders() : void {
    $arrayProvider = $this->createMock(iArray::class);
    $arrayProvider->expects(self::once())->method('getDefinitions')->willReturn(['array definitions']);

    $nestedFileProvider = $this->createMock(iFiles::class);
    $nestedFileProvider->expects(self::once())->method('getFiles')->willReturn(['some', 'files']);

    $nestedMetaprovider = $this->createMock(iMetaProvider::class);
    $nestedMetaprovider->expects(self::once())->method('getProviders')->willReturn([$nestedFileProvider]);

    $metaProvider = $this->createMock(iMetaProvider::class);
    $metaProvider->expects(self::once())->method('getProviders')->willReturn([$arrayProvider, $nestedMetaprovider]);

    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);
    $containerBuilder
      ->expects(self::exactly(3))
      ->method('addDefinitions')
      ->withConsecutive(
          [['array definitions']],
          ['some'],
          ['files']
      )
    ;
    $this->sut->setContainerBuilder($containerBuilder);

    $this->sut->addDefinitionsByProvider($metaProvider);
  }

  public function testBuild() : void {
    $container = $this->createMock(iContainer::class);

    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);
    $containerBuilder->expects(self::once())->method('build')->willReturn($container);
    $this->sut->setContainerBuilder($containerBuilder);

    self::assertSame($container, $this->sut->build());
  }

  public function testBuild_canThrowException_whenBuiltContainerIsNotOfTypeiContainer() : void {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Built container is not of type de\codenamephp\platform\di\iContainer');

    $containerBuilder = $this->createMock(\DI\ContainerBuilder::class);
    $containerBuilder->expects(self::once())->method('build')->willReturn(new stdClass());
    $this->sut->setContainerBuilder($containerBuilder);

    $this->sut->build();
  }
}
