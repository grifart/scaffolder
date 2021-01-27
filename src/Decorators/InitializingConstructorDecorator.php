<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator as Code;

final class InitializingConstructorDecorator implements ClassDecorator
{

	public function decorate(Code\PhpNamespace $namespace, ClassType $classType, ClassDefinition $definition): void
	{
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		$constructor = $classType->addMethod('__construct');
		$constructor->setVisibility('public');

		foreach ($definition->getFields() as $fieldName => $type) {
			$parameter = $constructor->addParameter($fieldName);
			$parameter->setType($type->getTypeHint());
			$parameter->setNullable($type->isNullable());

			$constructor->addBody('$this->? = ?;', [
				$fieldName,
				new Code\PhpLiteral('$' . $fieldName),
			]);

			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($namespace);

				$constructor->addComment(\sprintf(
					'@param %s $%s%s',
					$docCommentType,
					$fieldName,
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));
			}
		}

	}
}
