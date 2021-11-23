<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class Properties implements Capability
{
	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		foreach ($definition->getFields() as $field) {
			$type = $field->getType();
			// add property
			$property = $draft->getClassType()->addProperty($field->getName())
				->setVisibility('private')
				->setType($type->getTypeHint())
				->setNullable($type->isNullable());

			if ($type->requiresDocComment()) {
				$property->addComment(\sprintf(
					'@var %s',
					$type->getDocCommentType($draft->getNamespace()),
				));
			}
		}
	}
}
