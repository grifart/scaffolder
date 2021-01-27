<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

final class GettersDecorator implements ClassDecorator
{

	public function decorate(PhpNamespace $namespace, ClassType $classType, ClassDefinition $definition): void
	{
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
				$docCommentType = $type->getDocCommentType($namespace);

				$getter->addComment(\sprintf(
					'@return %s',
					$docCommentType,
				));
			}
		}
	}
}
