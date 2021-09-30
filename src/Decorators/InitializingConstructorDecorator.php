<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator as Code;

final class InitializingConstructorDecorator implements ClassDecorator
{

	public function decorate(ClassInNamespace $classInNamespace, ClassDefinition $definition): void
	{
		$classType = $classInNamespace->getClassType();
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		$constructor = $classType->addMethod('__construct');
		$constructor->setVisibility('public');

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			$parameter = $constructor->addParameter($fieldName);
			$parameter->setType($type->getTypeHint());
			$parameter->setNullable($type->isNullable());

			$constructor->addBody('$this->? = ?;', [
				$fieldName,
				new Code\PhpLiteral('$' . $fieldName),
			]);

			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($classInNamespace->getNamespace());

				$constructor->addComment(\sprintf(
					'@param %s $%s',
					$docCommentType,
					$fieldName,
				));
			}
		}

	}
}
