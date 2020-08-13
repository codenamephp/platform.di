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

use de\codenamephp\platform\di\definitionsProvider\dependency\iCoversDependencies;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependency;
use de\codenamephp\platform\di\definitionsProvider\dependency\iDependsOn;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use PHPUnit\Framework\TestCase;

class ClassNamesInArrayTest extends TestCase {

  private ClassNamesInArray $sut;

  protected function setUp() : void {
    $this->sut = new ClassNamesInArray();
  }

  public function test__construct_canSetCollectionWhenNoCollectionWasPassed() : void {
    self::assertInstanceOf(SimpleArray::class, $this->sut->getCollection());
  }

  public function test__construct_canSetCollection() : void {
    $collection = $this->createMock(iCollection::class);

    self::assertSame($collection, (new ClassNamesInArray($collection))->getCollection());
  }

  public function testsetCollection() : void {
    $collection = $this->createMock(iCollection::class);
    $this->sut->setCollection($collection);

    self::assertSame($collection, $this->sut->getCollection());
  }

  public function testadd_canThrowException_whenProviderImplementsiDependsOnInterface_andDependenciesAreMissing() : void {
    $this->sut->setCollectedDependencies(['dep 2']);

    $provider = $this->createMock(iDependsOn::class);
    $provider->expects(self::exactly(2))->method('getDependencies')->willReturn(['dep 1', 'dep 2', 'dep 3']);

    $this->expectException(MissingDependencyException::class);
    $this->expectExceptionMessage(sprintf(
        <<<EXCEPTION
      The provider "%s" is missing dependencies. Plaese add them to the container before adding this provider. Missing dependencies:
      [
        %s
      ]
      EXCEPTION, get_class($provider), implode("\n\t", ['dep 1', 'dep 3'])));

    $this->sut->add($provider);
  }

  public function testadd_canDetectDependencies() : void {
    $this->sut->setCollectedDependencies(['dep 1', 'dep 2', 'dep 3']);

    $provider = $this->getMockBuilder(iDependsOn::class)->getMock();
    $provider->expects(self::once())->method('getDependencies')->willReturn(['dep 1', 'dep 2', 'dep 3']);

    $this->sut->add($provider);
    self::assertSame([$provider], $this->sut->get());
  }

  public function testadd_canAddUniqueDependencies_fromgetCoveredDependencies_ifProviderImplementsiCoversDependenciesInterface() : void {
    $this->sut->setCollectedDependencies(['dep 1', 'dep 2', 'dep 3']);

    $provider = $this->getMockBuilder(iCoversDependencies::class)->getMock();
    $provider->expects(self::once())->method('getCoveredDependencies')->willReturn(['dep 2', 'dep 4']);

    $this->sut->add($provider);

    self::assertEquals(['dep 1', 'dep 2', 'dep 3', 'dep 4'], $this->sut->getCollectedDependencies());
    self::assertSame([$provider], $this->sut->get());
  }

  public function testadd_canAddUniqueDependencies_fromProviderClassName_ifProviderDoesNotImplementiCoversDependenciesInterface() : void {
    $this->sut->setCollectedDependencies(['dep 1', 'dep 2', 'dep 3']);

    $provider = $this->getMockBuilder(iDependency::class)->getMock();

    $this->sut->add($provider);

    self::assertEquals(['dep 1', 'dep 2', 'dep 3', get_class($provider)], $this->sut->getCollectedDependencies());
    self::assertSame([$provider], $this->sut->get());
  }

  public function testAddDependencies() : void {
    $this->sut->setCollectedDependencies(['dep 1', 'dep 2']);

    $this->sut->addDependencies(['dep 2', 'dep 3']);

    self::assertSame(['dep 1', 'dep 2', 'dep 3'], $this->sut->getCollectedDependencies());
  }
}
