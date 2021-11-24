<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\getters;
use function Grifart\ClassScaffolder\Capabilities\properties;

require __DIR__ . '/../bootstrap.php';

final class GettersTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
			getters(),
		];
	}
}

(new GettersTest())->run();
