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

namespace de\codenamephp\platform\di\definitionsProvider\collection;

use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;
use MJS\TopSort\TopSortInterface;
use PHPUnit\Framework\TestCase;

class TopoGraphTest extends TestCase {

  private TopoGraph $sut;

  protected function setUp() : void {
    $sort = $this->createMock(TopSortInterface::class);

    $this->sut = new TopoGraph($sort);
  }

  public function testAdd() : void {
    $provider = $this->createMock(iDependsOn::class);
    $provider->expects(self::once())->method('getDependencies')->willReturn(['depA', 'depB']);

    $sort = $this->createMock(TopSortInterface::class);
    $sort->expects(self::once())->method('add')->with(get_class($provider), ['depA', 'depB']);
    $this->sut->setSort($sort);

    $this->sut->add($provider);

    self::assertSame([get_class($provider) => $provider], $this->sut->getProviders());
  }

  public function testAdd_withoutDependency() : void {
    $provider = $this->createMock(iDefinitionsProvider::class);

    $sort = $this->createMock(TopSortInterface::class);
    $sort->expects(self::once())->method('add')->with(get_class($provider), []);
    $this->sut->setSort($sort);

    $this->sut->add($provider);

    self::assertSame([get_class($provider) => $provider], $this->sut->getProviders());
  }

  public function testget() : void {
    $provider1 = $this->createMock(iDefinitionsProvider::class);
    $provider2 = $this->createMock(iDefinitionsProvider::class);
    $provider3 = $this->createMock(iDefinitionsProvider::class);
    $provider4 = $this->createMock(iDefinitionsProvider::class);
    $provider5 = $this->createMock(iDefinitionsProvider::class);
    $provider6 = $this->createMock(iDefinitionsProvider::class);
    $this->sut->setProviders([
        'dep1' => $provider3,
        'dep2' => $provider1,
        'dep3' => $provider5,
        'dep4' => $provider4,
        'dep5' => $provider2,
        'dep6' => $provider6,
        'dep7' => null,
    ]);

    $sort = $this->createMock(TopSortInterface::class);
    $sort->expects(self::once())->method('sort')->willReturn(['dep2', 'dep5', 'dep1', 'dep4', 'dep3', 'dep8']);
    $this->sut->setSort($sort);

    self::assertSame([$provider1, $provider2, $provider3, $provider4, $provider5, $provider6], $this->sut->get());
  }

  public function testget_canRethrowCircularDependencyException() : void {
    $this->expectException(\de\codenamephp\platform\di\definitionsProvider\dependency\CircularDependencyException::class);
    $this->expectExceptionCode(0);

    $sort = $this->createMock(TopSortInterface::class);
    $sort->expects(self::once())->method('sort')->willThrowException($this->createMock(CircularDependencyException::class));
    $this->sut->setSort($sort);

    $this->sut->get();
  }

  public function testget_canRethrowElementNotFoundDependencyException() : void {
    $this->expectException(MissingDependencyException::class);
    $this->expectExceptionCode(0);

    $sort = $this->createMock(TopSortInterface::class);
    $sort->expects(self::once())->method('sort')->willThrowException($this->createMock(ElementNotFoundException::class));
    $this->sut->setSort($sort);

    $this->sut->get();
  }

  public function test__construct_canSetPassedSort() : void {
    $sort = $this->createMock(TopSortInterface::class);

    $sut = new TopoGraph($sort);

    self::assertSame($sort, $sut->getSort());
  }

  public function test__construct_canSetDefaultSort() : void {
    $sut = new TopoGraph();

    self::assertInstanceOf(StringSort::class, $sut->getSort());
  }
}

