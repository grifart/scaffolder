<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;

final class PropertiesDecorator implements ClassDecorator
{


	public function decorate(ClassType $classType, ClassDefinition $definition): void
	{
		$namespace = $classType->getNamespace();
		\assert($namespace !== NULL, 'Class Generator always generate class in namespace.');

		foreach ($definition->getFields() as $fieldName => $type) {
			// add property
			$classType->addProperty($fieldName)
				->setVisibility('private')
				->addComment(\sprintf(
					'@var %s%s',
					$type->getDocCommentType($namespace),
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));
		}
	}
}