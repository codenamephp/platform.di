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
use de\codenamephp\platform\di\definitionsProvider\factory\byClassname\iByClassname;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;
use PHPUnit\Framework\TestCase;

class CreateAndAddDependenciesBeforeProviderTest extends TestCase {

  private CreateAndAddDependenciesBeforeProvider $sut;

  protected function setUp() : void {
    $providerCollection = $this->createMock(iCollection::class);
    $byClassnameProviderFactory = $this->createMock(iByClassname::class);

    $this->sut = new CreateAndAddDependenciesBeforeProvider($providerCollection, $byClassnameProviderFactory);
  }

  public function test__construct_canUseArgumentsFromConstructor() : void {
    $providerCollection = $this->createMock(iCollection::class);
    $byClassnameProviderFactory = $this->createMock(iByClassname::class);

    $this->sut = new CreateAndAddDependenciesBeforeProvider($providerCollection, $byClassnameProviderFactory);

    self::assertSame($providerCollection, $this->sut->getProviderCollection());
    self::assertSame($byClassnameProviderFactory, $this->sut->getByClassnameProviderFactory());
  }

  public function testAdd() : void {
    $dependency1 = $this->createMock(iDependsOn::class);
    $dependency1->expects(self::once())->method('getDependencies')->willReturn([]);
    $dependency2 = $this->createMock(iDefinitionsProvider::class);
    $dependency3 = $this->createMock(iDependsOn::class);
    $dependency3->expects(self::never())->method('getDependencies');

    $provider = $this->createMock(iDependsOn::class);
    $provider->expects(self::once())->method('getDependencies')->willReturn([get_class($dependency1), get_class($dependency2), get_class($dependency3)]);

    $providerCollection = $this->createMock(iCollection::class);
    $providerCollection
        ->expects(self::exactly(3))
        ->method('add')
        ->withConsecutive(
            [$dependency1],
            [$dependency2],
            [$provider]
        );
    $this->sut->setProviderCollection($providerCollection);

    $byClassnameProviderFactory = $this->createMock(iByClassname::class);
    $byClassnameProviderFactory
        ->expects(self::exactly(2))
        ->method('build')
        ->withConsecutive(
            [get_class($dependency1)],
            [get_class($dependency2)],
        )
        ->willReturnOnConsecutiveCalls($dependency1, $dependency2, $dependency3);
    $this->sut->setByClassnameProviderFactory($byClassnameProviderFactory);

    $this->sut->add($provider);

    self::assertEquals([
        get_class($dependency1),
        get_class($dependency2),
        get_class($provider),
    ], $this->sut->getAddedProviderClassnames());
  }

  public function testGet() : void {
    $providers = [
        $this->createMock(iDefinitionsProvider::class),
        $this->createMock(iDefinitionsProvider::class),
        $this->createMock(iDefinitionsProvider::class),
    ];

    $providerCollection = $this->createMock(iCollection::class);
    $providerCollection->expects(self::once())->method('get')->willReturn($providers);
    $this->sut->setProviderCollection($providerCollection);

    self::assertSame($providers, $this->sut->get());
  }
}
