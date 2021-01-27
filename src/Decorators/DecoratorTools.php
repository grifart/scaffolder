<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\InvalidArgumentException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PromotedParameter;
use Nette\PhpGenerator\Property;

/** @internal used by {@see ClassDecorator} */
final class DecoratorTools
{
	public static function checkIfAllFieldsArePresent(ClassDefinition $definition, ClassType $classType): void
	{
		// check if all fields are present
		foreach ($definition->getFields() as $field) {
			try {
				self::getProperty($classType, $field->getName());
			} catch (\InvalidArgumentException $e) {
				throw new \InvalidArgumentException('Used decorator requires you to have all fields specified in class specification already declared in generated class. Maybe you want to use PropertiesDecorator before this one?', 0, $e);
			}
		}
	}

	public static function getProperty(ClassType $classType, string $name): Property|PromotedParameter
	{
		try {
			return $classType->getProperty($name);
		} catch (InvalidArgumentException) {
			$promotedParameter = $classType->getMethod('__construct')->getParameters()[$name] ?? null;
			if ($promotedParameter === null || ! $promotedParameter instanceof PromotedParameter) {
				throw new \InvalidArgumentException(\sprintf('Property $%s does not exist on class %s.', $name, $classType->getName()));
			}

			return $promotedParameter;
		}
	}
}
