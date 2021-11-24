<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use Grifart\ClassScaffolder\Capabilities\Decorator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;

require __DIR__ . '/../bootstrap.php';

final class DecoratorTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [new Decorator(new PropertiesDecorator())];
	}
}

(new DecoratorTest())->run();
