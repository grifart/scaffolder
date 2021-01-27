<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;


final class PropertiesDecorator implements ClassDecorator
{


	public function decorate(PhpNamespace $namespace, ClassType $classType, ClassDefinition $definition): void
	{
		foreach ($definition->getFields() as $fieldName => $type) {
			// add property
			$property = $classType->addProperty($fieldName)
				->setVisibility('private')
				->setType($type->getTypeHint())
				->setNullable($type->isNullable());

			if ($type->requiresDocComment()) {
				$property->addComment(\sprintf(
					'@var %s%s',
					$type->getDocCommentType($namespace),
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));
			}
		}
	}
}
