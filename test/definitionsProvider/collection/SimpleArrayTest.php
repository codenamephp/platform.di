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

use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;
use PHPUnit\Framework\TestCase;

class SimpleArrayTest extends TestCase {

  private SimpleArray $sut;

  protected function setUp() : void {
    $this->sut = new SimpleArray();
  }

  public function testAddAndGet() : void {
    $provider1 = $this->createMock(iDefintionsProvider::class);
    $provider2 = $this->createMock(iDefintionsProvider::class);
    $provider3 = $this->createMock(iDefintionsProvider::class);

    $this->sut
        ->add($provider1)
        ->add($provider2)
        ->add($provider3);

    self::assertSame([$provider1, $provider2, $provider3], $this->sut->get());
  }
}
