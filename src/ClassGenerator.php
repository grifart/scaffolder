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
		$draft = ClassInNamespace::fromDefinition($definition);
		$namespace = $draft->getNamespace();
		$classType = $draft->getClassType();
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

		$current = self::findCurrent($definition);
		foreach ($definition->getDecorators() as $decorator) {
			$decorator->decorate($definition, $draft, $current);
		}


		return $namespace;
	}


	private static function findCurrent(ClassDefinition $definition): ?ClassInNamespace
	{
		$namespace = $definition->getNamespaceName();
		$className = $definition->getClassName();
		$classFqn = ($namespace === null ? '' : $namespace) . '\\' . $className;
		if ( ! \class_exists($classFqn)) {
			return null;
		}

		// find class' namespace
		$file = Code\PhpFile::fromCode(\file_get_contents((new \ReflectionClass($classFqn))->getFileName()));
		$matchedNamespace = null;
		foreach ($file->getNamespaces() as $namespace) {
			$doesNamespaceContainDesiredClass = \count(\array_filter($namespace->getClasses(), fn(Code\ClassType $classType): bool => $classType->getName() === $className)) === 1;
			if ($doesNamespaceContainDesiredClass) {
				$matchedNamespace = $namespace;
				break;
			}
		}
		\assert($matchedNamespace !== null);

		return ClassInNamespace::from(
			$matchedNamespace,
			Code\ClassType::withBodiesFrom($classFqn),
		);
	}

}
