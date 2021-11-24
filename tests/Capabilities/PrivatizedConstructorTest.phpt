<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\initializingConstructor;
use function Grifart\ClassScaffolder\Capabilities\privatizedConstructor;
use function Grifart\ClassScaffolder\Capabilities\properties;

require __DIR__ . '/../bootstrap.php';

final class PrivatizedConstructorTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
			initializingConstructor(),
			privatizedConstructor(),
		];
	}
}

(new PrivatizedConstructorTest())->run();
