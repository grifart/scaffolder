<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\CheckedClassType;
use Grifart\ClassScaffolder\Definition\Types\ClassType;
use Grifart\ClassScaffolder\Definition\Types\CompositeType;
use Nette\PhpGenerator as Code;


final class ClassGenerator
{

	public function generateClass(ClassDefinition $definition): Code\PhpFile
	{
		$draft = ClassInNamespace::fromDefinition($definition);
		$namespace = $draft->getNamespace();
		$classType = $draft->getClassType();
		$classType->setFinal();


		// fields – always set use statements for defined fields (so that one can refer it in whatever capability)

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


		// capabilities

		$current = self::findCurrent($definition);
		foreach ($definition->getCapabilities() as $capability) {
			$capability->applyTo($definition, $draft, $current);
		}


		$file = new Code\PhpFile();
		$file->setStrictTypes();
		$file->addNamespace($namespace);

		$file->addComment('Do not edit. This is generated file. Modify definition file instead.');

		return $file;
	}


	private static function findCurrent(ClassDefinition $definition): ?ClassInNamespace
	{
		$className = $definition->getClassName();
		$classFqn = $definition->getFullyQualifiedName();
		if ( ! \class_exists($classFqn)) {
			return null;
		}

		// find class' namespace
		$classFile = (new \ReflectionClass($classFqn))->getFileName();
		if ($classFile === false) {
			throw new \LogicException('Cannot copy from core or extension class ' . $classFqn);
		}

		$classFileContent = \file_get_contents($classFile);
		\assert($classFileContent !== false);

		$file = Code\PhpFile::fromCode($classFileContent);
		$matchedNamespace = null;
		foreach ($file->getNamespaces() as $namespace) {
			$doesNamespaceContainDesiredClass = \count(\array_filter($namespace->getClasses(), fn(Code\ClassLike $classType): bool => $classType->getName() === $className)) === 1;
			if ($doesNamespaceContainDesiredClass) {
				$matchedNamespace = $namespace;
				break;
			}
		}
		\assert($matchedNamespace !== null);

		$classType = Code\ClassType::from($classFqn, withBodies: true);
		\assert($classType instanceof Code\ClassType);

		return ClassInNamespace::from(
			$matchedNamespace,
			$classType,
		);
	}

}
