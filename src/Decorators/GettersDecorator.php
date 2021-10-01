<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class GettersDecorator implements ClassDecorator
{

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		$classType = $draft->getClassType();
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			// add getter
			$getter = $classType->addMethod('get' . \ucfirst($fieldName))
				->setVisibility('public')
				->addBody('return $this->?;', [
					$fieldName,
				]);

			$getter->setReturnType($type->getTypeHint());
			$getter->setReturnNullable($type->isNullable());


			// add phpDoc type hints if necessary
			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($draft->getNamespace());

				$getter->addComment(\sprintf(
					'@return %s',
					$docCommentType,
				));
			}
		}
	}
}
