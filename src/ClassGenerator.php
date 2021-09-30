<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Grifart\ClassScaffolder\Definition\Types\ClassType;
use Grifart\ClassScaffolder\Definition\Types\CompositeType;
use Grifart\ClassScaffolder\Definition\Types\Type;
use Nette\PhpGenerator as Code;


final class ClassGenerator
{

	public function generateClass(ClassDefinition $definition): Code\PhpNamespace
	{
		$classInNamespace = ClassInNamespace::fromDefinition($definition);
		$namespace = $classInNamespace->getNamespace();
		$classType = $classInNamespace->getClassType();
		$classType->setFinal();


		// GLOBAL STUFF

		// implements

		foreach ($definition->getImplements() as $implement) {
			$namespace->addUse($implement);
			$classType->addImplement($implement);
		}


		// fields – always set use statements for defined fields (so that one can refer it in whatever decorator)

		foreach ($definition->getFields() as $field) {

			// add use
			$addUse = static function (array $types) use ($namespace, &$addUse): void {
				/** @var Definition\Types\Type $type */
				foreach ($types as $type) {
					if ($type instanceof CompositeType) {
						$addUse($type->getSubTypes());

					} elseif ($type instanceof ClassType) {
						$namespace->addUse($type->getTypeName());
					}
				}
			};

			$addUse([$field->getType()]);

		}


		// decorators

		foreach ($definition->getDecorators() as $decorator) {
			$decorator->decorate($classInNamespace, $definition);
		}


		return $namespace;
	}

}
