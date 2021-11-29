<?php

use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Definition\definitionOf;

return [
	definitionOf(UnmodifiedClass::class)
		->withField('field', 'string')
		->with(constructorWithPromotedProperties()),

	definitionOf(ModifiedClass::class)
		->withField('field', 'string')
		->with(constructorWithPromotedProperties()),

	definitionOf(MissingClass::class)
		->withField('field', 'string')
		->with(constructorWithPromotedProperties()),
];
