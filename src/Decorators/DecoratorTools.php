<?php declare(strict_types=1);


namespace Grifart\ClassScaffolder\Decorators;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/** @internal used by {@see ClassDecorator} */
final class DecoratorTools
{
	public static function checkIfAllFieldsArePresent(ClassDefinition $definition, ClassType $classType): void
	{
		// check if all fields are present
		foreach ($definition->getFields() as $fieldName => $type) {
			try {
				$classType->getProperty($fieldName);
			} catch (\Nette\InvalidArgumentException $e) {
				throw new \InvalidArgumentException('Used decorator requires you to have all fields specified in class specification already declared in generated class. Maybe you want to use PropertiesDecorator before this one?', 0, $e);
			}
		}
	}
}
