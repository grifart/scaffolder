<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpLiteral;
use Nette\PhpGenerator\PhpNamespace;


final class SettersDecorator implements ClassDecorator
{

	public function decorate(PhpNamespace $namespace, ClassType $classType, ClassDefinition $definition): void
	{
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		foreach ($definition->getFields() as $fieldName => $type) {
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
