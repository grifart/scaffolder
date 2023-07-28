<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Capabilities\readonlyClass;

require __DIR__ . '/../bootstrap.php';

final class ReadonlyClassTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			constructorWithPromotedProperties(),
			readonlyClass(),
		];
	}
}

(new ReadonlyClassTest())->run();
