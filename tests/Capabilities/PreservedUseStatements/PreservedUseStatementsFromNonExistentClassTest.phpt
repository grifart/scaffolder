<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedUseStatements;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Grifart\ClassScaffolder\Test\Capabilities\CapabilityTestCase;
use function Grifart\ClassScaffolder\Capabilities\preservedUseStatements;
use function Grifart\ClassScaffolder\Capabilities\properties;
use function Grifart\ClassScaffolder\Definition\definitionOf;

require __DIR__ . '/../../bootstrap.php';

final class PreservedUseStatementsFromNonExistentClassTest extends CapabilityTestCase
{
	protected function getCapabilities(): array
	{
		return [
			properties(),
			preservedUseStatements(),
		];
	}

	protected function createDefinition(): ClassDefinition
	{
		return definitionOf(NonExistentClass::class)
			->withField('field', Field::class);
	}
}

(new PreservedUseStatementsFromNonExistentClassTest())->run();
