<?php

declare(strict_types = 1);

namespace Doklady\Scaffolder\Decorators;

use Nette\PhpGenerator\ClassType;


interface ClassDecorator
{

	public function decorate(ClassType $classType): void;

}
