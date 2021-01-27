<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

final class ConstructorWithPromotedPropertiesDecorator
{
	public function decorate(PhpNamespace $namespace, ClassType $classType, ClassDefinition $definition): void
	{
		$constructor = $classType->addMethod('__construct');
		$constructor->setVisibility('public');

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			$constructor->addPromotedParameter($fieldName)
				->setPrivate()
				->setType($type->getTypeHint())
				->setNullable($type->isNullable());

			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($namespace);
				$constructor->addComment(\sprintf(
					'@param %s $%s',
					$docCommentType,
					$fieldName,
				));
			}
		}
	}
}
