<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\InitializingConstructor;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * @deprecated Use {@see InitializingConstructor} capability instead
 */
final class InitializingConstructorDecorator implements ClassDecorator
{
	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new InitializingConstructor())->applyTo($definition, $draft, $current);
	}
}
