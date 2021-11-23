<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use function Grifart\ClassScaffolder\Capabilities\preservedMethod;

/**
 * ⚠ Note that for transferring use statements you should use
 * `KeepUseStatementsDecorator` as well. Call it before this one.
 */
final class KeepMethodDecorator implements ClassDecorator
{
	public function __construct(
		private string $methodToBeKept,
	) {}

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		preservedMethod($this->methodToBeKept)->applyTo($definition, $draft, $current);
	}
}
