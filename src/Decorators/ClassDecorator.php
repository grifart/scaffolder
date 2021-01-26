<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

interface ClassDecorator
{

	public function decorate(
		PhpNamespace $namespace,
		ClassType $classType,
		ClassDefinition $definition,
	): void;

}
