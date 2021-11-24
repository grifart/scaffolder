<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Capabilities\readonlyProperties;

require __DIR__ . '/../bootstrap.php';

final class ReadonlyPropertiesTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			constructorWithPromotedProperties(),
			readonlyProperties(),
		];
	}
}

(new ReadonlyPropertiesTest())->run();
