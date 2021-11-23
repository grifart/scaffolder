<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\KeepMethod;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use function Grifart\ClassScaffolder\Capabilities\preservedAnnotatedMethods;

/**
 * ⚠ Note that for transferring use statements you should use
 * `KeepUseStatementsDecorator` as well. Call it before this one.
 */
final class KeepAnnotatedMethodsDecorator implements ClassDecorator
{
	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		preservedAnnotatedMethods()->applyTo($definition, $draft, $current);
	}
}
