<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\initializingConstructor;
use function Grifart\ClassScaffolder\Capabilities\privateConstructor;
use function Grifart\ClassScaffolder\Capabilities\properties;

require __DIR__ . '/../bootstrap.php';

final class PrivateConstructorTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
			initializingConstructor(),
			privateConstructor(),
		];
	}
}

(new PrivateConstructorTest())->run();
