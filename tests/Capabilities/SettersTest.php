<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\properties;
use function Grifart\ClassScaffolder\Capabilities\setters;

require __DIR__ . '/../bootstrap.php';

final class SettersTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
			setters(),
		];
	}
}

(new SettersTest())->run();
