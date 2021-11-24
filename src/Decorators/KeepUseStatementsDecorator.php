<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\PreservedUseStatements;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * @deprecated Use {@see PreservedUseStatements} capability instead
 */
final class KeepUseStatementsDecorator implements ClassDecorator
{
	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new PreservedUseStatements())->applyTo($definition, $draft, $current);
	}
}
