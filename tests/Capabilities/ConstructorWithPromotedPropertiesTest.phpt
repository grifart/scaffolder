<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;

require __DIR__ . '/../bootstrap.php';

final class ConstructorWithPromotedPropertiesTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [constructorWithPromotedProperties()];
	}
}

(new ConstructorWithPromotedPropertiesTest())->run();
