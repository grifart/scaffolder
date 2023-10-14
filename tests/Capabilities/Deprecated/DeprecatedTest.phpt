<?php declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Test\Capabilities\Deprecated;

use Grifart\ClassScaffolder\Test\Capabilities\CapabilityTestCase;
use function Grifart\ClassScaffolder\Capabilities\deprecated;


require __DIR__ . '/../../bootstrap.php';

final class DeprecatedTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			deprecated(Replacement::class),
		];
	}
}

(new DeprecatedTest())->run();
