<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedAnnotatedMethods;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Test\Capabilities\CapabilityTestCase;
use function Grifart\ClassScaffolder\Capabilities\preservedAnnotatedMethods;
use function Grifart\ClassScaffolder\Capabilities\preservedUseStatements;
use function Grifart\ClassScaffolder\Definition\define;

require __DIR__ . '/../../bootstrap.php';

final class PreservedAnnotatedMethodsTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			preservedUseStatements(),
			preservedAnnotatedMethods(),
		];
	}

	protected function createDefinition(): ClassDefinition
	{
		return define(ExistentClass::class);
	}
}

(new PreservedAnnotatedMethodsTest())->run();
