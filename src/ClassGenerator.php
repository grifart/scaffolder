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


		// uses

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

		}


		// decorators

		foreach ($definition->getDecorators() as $decorator) {
			$decorator->decorate($classType, $definition);
		}


		return $namespace;
	}

}
