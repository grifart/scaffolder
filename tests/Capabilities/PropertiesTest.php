<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\properties;

require __DIR__ . '/../bootstrap.php';

final class PropertiesTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
		];
	}
}

(new PropertiesTest())->run();
