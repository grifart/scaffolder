<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\implementedInterface;

require __DIR__ . '/../bootstrap.php';

final class ImplementedInterfaceTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			implementedInterface(\Iterator::class),
		];
	}
}

(new ImplementedInterfaceTest())->run();
