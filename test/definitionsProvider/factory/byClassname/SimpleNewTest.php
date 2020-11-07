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

namespace de\codenamephp\platform\di\definitionsProvider\factory\byClassname;

use de\codenamephp\platform\di\definitionsProvider\factory\ProviderCouldNotBeCreatedException;
use de\codenamephp\platform\di\definitionsProvider\factory\ProviderDoesNotImplementProviderInterfaceException;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;
use PHPUnit\Framework\TestCase;

class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    $this->sut = new SimpleNew();
  }

  public function testBuild() : void {
    $provider = $this->sut->build(WithInterface::class, 123, '456', 123.456);

    /** @var WithInterface $provider */
    self::assertInstanceOf(WithInterface::class, $provider);
    self::assertSame(123, $provider->a);
    self::assertSame('456', $provider->b);
    self::assertSame(123.456, $provider->c);
  }

  public function testCanThrowException_WhenErrorOccurs() : void {
    $this->expectException(ProviderCouldNotBeCreatedException::class);
    $this->expectExceptionMessage(<<<'MESSAGE'
      Could not create provider de\codenamephp\platform\di\definitionsProvider\factory\byClassname\WithError with arguments array (
      )
      MESSAGE
    );
    $this->sut->build(WithError::class);
  }

  public function testCanThrowException_WhenProviderDoesNotImplementProviderInterface() : void {
    $this->expectException(ProviderDoesNotImplementProviderInterfaceException::class);
    $this->expectExceptionMessage('Created provider de\codenamephp\platform\di\definitionsProvider\factory\byClassname\WithoutInterface does not implement de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider');

    $this->sut->build(WithoutInterface::class);
  }
}

class WithInterface implements iDefinitionsProvider {
  public int $a;

  public string $b;

  public float $c;

  public function __construct($a, $b, $c) {
    $this->a = $a;
    $this->b = $b;
    $this->c = $c;
  }
}

class WithError {
  public function __construct() {
    trigger_error('', E_ERROR);
  }
}

class WithoutInterface {

}