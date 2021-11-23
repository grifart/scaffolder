<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use function Grifart\ClassScaffolder\Capabilities\statefulImplementation;

final class StatefulDecorator implements ClassDecorator
{
	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		statefulImplementation()->applyTo($definition, $draft, $current);
	}
}
