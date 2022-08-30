<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedMethod;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Test\Capabilities\CapabilityTestCase;
use function Grifart\ClassScaffolder\Capabilities\preservedMethod;
use function Grifart\ClassScaffolder\Capabilities\preservedUseStatements;
use function Grifart\ClassScaffolder\Definition\definitionOf;

require __DIR__ . '/../../bootstrap.php';

final class PreservedMethodTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			preservedUseStatements(),
			preservedMethod('newMethod'),
			preservedMethod('preservedMethod'),
			preservedMethod('preservedMethodWithParam'),
			preservedMethod('preservedMethodWithPhpDocParam'),
			preservedMethod('preservedMethodWithImportedUses'),
			preservedMethod('preservedMethodWithAnnotation'),
		];
	}

	protected function createDefinition(): ClassDefinition
	{
		return definitionOf(ExistentClass::class);
	}
}

(new PreservedMethodTest())->run();
