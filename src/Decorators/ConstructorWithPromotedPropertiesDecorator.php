<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class ConstructorWithPromotedPropertiesDecorator implements ClassDecorator
{
	public function decorate(ClassDefinition $definition, ClassInNamespace $draft): void
	{
		$constructor = $draft->getClassType()->addMethod('__construct');
		$constructor->setVisibility('public');

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			$constructor->addPromotedParameter($fieldName)
				->setPrivate()
				->setType($type->getTypeHint())
				->setNullable($type->isNullable());

			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($draft->getNamespace());
				$constructor->addComment(\sprintf(
					'@param %s $%s',
					$docCommentType,
					$fieldName,
				));
			}
		}
	}
}
