<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\ClassType;
use Grifart\ClassScaffolder\Definition\Types\CompositeType;
use Nette\PhpGenerator as Code;


final class ClassGenerator
{

	public function generateClass(ClassDefinition $definition): Code\PhpNamespace
	{
		$namespace = new Code\PhpNamespace($definition->getNamespaceName() ?? '');
		$classType = $namespace->addClass($definition->getClassName());
		$classType->setFinal();


		// implements

		foreach ($definition->getImplements() as $implement) {
			$classType->getNamespace()->addUse($implement);
			$classType->addImplement($implement);
		}


		// constructor

		$constructor = $classType->addMethod('__construct');
		$constructor->setVisibility('public');


		// fields

		foreach ($definition->getFields() as $fieldName => $type) {
			// add use
			$addUse = function (array $types) use ($namespace, &$addUse): void {
				/** @var Definition\Types\Type[] $types */
				foreach ($types as $type) {
					if ($type instanceof CompositeType) {
						$addUse($type->getSubTypes());

					} elseif ($type instanceof ClassType) {
						$namespace->addUse($type->getTypeName());
					}
				}
			};

			$addUse([$type]);


			// add property
			$classType->addProperty($fieldName)
				->setVisibility('private')
				->addComment(\sprintf(
					'@var %s%s',
					$type->getDocCommentType($namespace),
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));


			// add constructor assignment
			$parameter = $constructor->addParameter($fieldName);
			$parameter->setTypeHint($type->getTypeHint());
			$parameter->setNullable($type->isNullable());

			$constructor->addBody('$this->? = ?;', [
				$fieldName,
				new Code\PhpLiteral('$' . $fieldName),
			]);


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

				$constructor->addComment(\sprintf(
					'@param %s $%s%s',
					$docCommentType,
					$fieldName,
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));

				$getter->addComment(\sprintf(
					'@return %s%s',
					$docCommentType,
					$type->hasComment() ? ' ' . $type->getComment($namespace) : ''
				));
			}
		}


		// decorators

		foreach ($definition->getDecorators() as $decorator) {
			$decorator->decorate($classType);
		}


		return $namespace;
	}

}
