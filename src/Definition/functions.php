<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;


function define(ClassDefinitionBuilder|string $definitionOrClassName): ClassDefinitionBuilder {
	if ($definitionOrClassName instanceof ClassDefinitionBuilder) {
		return $definitionOrClassName;
	}

	return new ClassDefinitionBuilder($definitionOrClassName);
}
