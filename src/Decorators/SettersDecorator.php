<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;


use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpLiteral;

final class SettersDecorator implements ClassDecorator
{

	public function decorate(ClassType $classType, ClassDefinition $definition): void
	{
		$namespace = $classType->getNamespace();
		\assert($namespace !== NULL, 'Class Generator always generate class in namespace.');

		foreach ($definition->getFields() as $fieldName => $type) {
			// add getter
			$getter = $classType->addMethod('set' . \ucfirst($fieldName))
				->setVisibility('public')
				->addBody('$this->? = ?;', [
					$fieldName,
					new PhpLiteral('$' . $fieldName),
				])
				->setParameters([
					(new Parameter($fieldName))->setTypeHint($type->getTypeHint())
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