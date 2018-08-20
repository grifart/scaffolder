<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;

final class GettersDecorator implements ClassDecorator
{

	public function decorate(ClassType $classType, ClassDefinition $definition): void
	{
		$namespace = DecoratorTools::extractNamespace($classType);
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		foreach ($definition->getFields() as $fieldName => $type) {
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
					'@return %s%s',
					$docCommentType,
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));
			}
		}
	}
}