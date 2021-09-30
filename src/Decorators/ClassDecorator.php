<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

interface ClassDecorator
{

	public function decorate(
		ClassInNamespace $classInNamespace,
		ClassDefinition $definition,
	): void;

}
