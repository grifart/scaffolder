<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use function Grifart\ClassScaffolder\Capabilities\initializingConstructor;

final class InitializingConstructorDecorator implements ClassDecorator
{

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		initializingConstructor()->applyTo($definition, $draft, $current);
	}
}
