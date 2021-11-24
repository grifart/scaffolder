<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\ConstructorWithPromotedProperties;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * @deprecated Use {@see ConstructorWithPromotedProperties} capability instead
 */
final class ConstructorWithPromotedPropertiesDecorator implements ClassDecorator
{
	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new ConstructorWithPromotedProperties())->applyTo($definition, $draft, $current);
	}
}
