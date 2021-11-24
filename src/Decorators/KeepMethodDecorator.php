<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\PreservedMethod;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * ⚠ Note that for transferring use statements you should use
 * `KeepUseStatementsDecorator` as well. Call it before this one.
 *
 * @deprecated Use {@see PreservedMethod} capability instead
 */
final class KeepMethodDecorator implements ClassDecorator
{
	public function __construct(
		private string $methodToBeKept,
	) {}

	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new PreservedMethod($this->methodToBeKept))->applyTo($definition, $draft, $current);
	}
}
