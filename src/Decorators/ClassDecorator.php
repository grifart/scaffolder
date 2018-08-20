<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;


interface ClassDecorator
{

	public function decorate(ClassType $classType, ClassDefinition $definition): void;

}
