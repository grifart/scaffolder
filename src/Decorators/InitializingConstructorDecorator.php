<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator as Code;

final class InitializingConstructorDecorator implements ClassDecorator
{

	public function decorate(ClassInNamespace $draft, ClassDefinition $definition): void
	{
		$classType = $draft->getClassType();
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
				$docCommentType = $type->getDocCommentType($draft->getNamespace());

				$constructor->addComment(\sprintf(
					'@param %s $%s',
					$docCommentType,
					$fieldName,
				));
			}
		}

	}
}
