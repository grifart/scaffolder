<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Capabilities\CapabilityTools;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PromotedParameter;
use Nette\PhpGenerator\Property;

/** @internal used by {@see ClassDecorator} */
final class DecoratorTools
{
	/**
	 * @deprecated use {@see CapabilityTools::checkIfAllFieldsArePresent()} instead
	 */
	public static function checkIfAllFieldsArePresent(ClassDefinition $definition, ClassType $classType): void
	{
		CapabilityTools::checkIfAllFieldsArePresent($definition, $classType);
	}

	/**
	 * @deprecated use {@see CapabilityTools::getProperty()} instead
	 */
	public static function getProperty(ClassType $classType, string $name): Property|PromotedParameter
	{
		return CapabilityTools::getProperty($classType, $name);
	}
}
