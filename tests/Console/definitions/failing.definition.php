<?php

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use function Grifart\ClassScaffolder\Definition\definitionOf;

return [
	definitionOf(SuccessClass::class)
		->withField('field', 'string'),

	definitionOf(FailingClass::class)
		->withField('field', 'string')
		->with(new class implements Capability {
			public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current,): void
			{
				throw new RuntimeException('Oh no, I failed :(');
			}
		}),
];
