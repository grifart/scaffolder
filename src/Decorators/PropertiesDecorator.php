<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\Properties;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * @deprecated Use {@see Properties} capability instead
 */
final class PropertiesDecorator implements ClassDecorator
{
	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new Properties())->applyTo($definition, $draft, $current);
	}
}
