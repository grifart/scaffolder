<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpLiteral;


final class SettersDecorator implements ClassDecorator
{

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		$classType = $draft->getClassType();
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			// add getter
			$getter = $classType->addMethod('set' . \ucfirst($fieldName))
				->setVisibility('public')
				->addBody('$this->? = ?;', [
					$fieldName,
					new PhpLiteral('$' . $fieldName),
				])
				->setParameters([
					(new Parameter($fieldName))
						->setType($type->getTypeHint())
						->setNullable($type->isNullable())
				]);
			$getter->setReturnType('void');


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
