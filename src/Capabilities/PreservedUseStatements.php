<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class PreservedUseStatements implements Capability
{
	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		if ($current === null) {
			return;
		}

		foreach ($current->getNamespace()->getUses() as $alias => $use) {
			$draft->getNamespace()->addUse($use, $alias);
		}
	}
}
