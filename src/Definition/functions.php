<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

/**
 * @param array<string, Types\Type|ClassDefinition|string> $withFields
 */
function definitionOf(
	ClassDefinition|string $definitionOrClassName,
	array $withFields = [],
): ClassDefinition {
	$definition = $definitionOrClassName instanceof ClassDefinition
		? $definitionOrClassName
		: new ClassDefinition($definitionOrClassName);

	return $definition->withFields($withFields);
}
