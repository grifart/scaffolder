<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;


final class PropertiesDecorator implements ClassDecorator
{


	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
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
