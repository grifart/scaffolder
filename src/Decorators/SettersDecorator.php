<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\Setters;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * @deprecated Use {@see Setters} capability instead
 */
final class SettersDecorator implements ClassDecorator
{
	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new Setters())->applyTo($definition, $draft, $current);
	}
}
