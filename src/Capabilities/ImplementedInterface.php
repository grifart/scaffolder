<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class ImplementedInterface implements Capability
{
	/**
	 * @param class-string $interfaceName
	 */
	public function __construct(
		private string $interfaceName,
	) {}

	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$draft->getNamespace()->addUse($this->interfaceName);
		$draft->getClassType()->addImplement($this->interfaceName);
	}
}
