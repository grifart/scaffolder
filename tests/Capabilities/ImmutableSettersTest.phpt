<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\immutableSetters;
use function Grifart\ClassScaffolder\Capabilities\properties;

require __DIR__ . '/../bootstrap.php';

final class ImmutableSettersTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
			immutableSetters('field2'),
		];
	}
}

(new ImmutableSettersTest())->run();
