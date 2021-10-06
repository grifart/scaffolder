<?php declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;


final class KeepUseStatementsDecorator implements ClassDecorator
{

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		if ($current === null) {
			return;
		}

		foreach ($current->getNamespace()->getUses() as $alias => $use) {
			$draft->getNamespace()->addUse($use, $alias);
		}
	}

}
