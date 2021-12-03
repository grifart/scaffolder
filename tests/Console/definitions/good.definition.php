<?php

use Grifart\ClassScaffolder\Capabilities;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use function Grifart\ClassScaffolder\Definition\definitionOf;

function valueObject(string $className): ClassDefinition {
	return definitionOf($className)->with(
		Capabilities\constructorWithPromotedProperties(),
		Capabilities\readonlyProperties(),
	);
}

return [
	$dataClass = definitionOf(valueObject(DataClass::class))
		->withField('field', 'string'),

	definitionOf(valueObject(AnotherClass::class))
		->withField('data', Types\listOf($dataClass)),
];
