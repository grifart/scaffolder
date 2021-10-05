<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

function define(ClassDefinition|string $definitionOrClassName): ClassDefinition {
	if ($definitionOrClassName instanceof ClassDefinition) {
		return $definitionOrClassName;
	}

	return new ClassDefinition($definitionOrClassName);
}
