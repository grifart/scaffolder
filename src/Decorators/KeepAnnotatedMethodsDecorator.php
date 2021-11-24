<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\PreservedAnnotatedMethods;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * ⚠ Note that for transferring use statements you should use
 * `KeepUseStatementsDecorator` as well. Call it before this one.
 *
 * @deprecated Use {@see PreservedAnnotatedMethods} capability instead
 */
final class KeepAnnotatedMethodsDecorator implements ClassDecorator
{
	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		(new PreservedAnnotatedMethods())->applyTo($definition, $draft, $current);
	}
}
