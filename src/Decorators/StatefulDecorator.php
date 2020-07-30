<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\Stateful\State;
use Grifart\Stateful\StateBuilder;
use Nette\PhpGenerator as Code;


final class StatefulDecorator implements ClassDecorator
{

	public function decorate(Code\ClassType $classType, ClassDefinition $definition): void
	{
		$namespace = DecoratorTools::extractNamespace($classType);
		DecoratorTools::checkIfAllFieldsArePresent($definition, $classType);

		$namespace->addUse(StateBuilder::class);
		$namespace->addUse(State::class);

		$getState = $classType->addMethod('_getState');
		$getState->setVisibility('public');
		$getState->setReturnType(State::class);
		$getState->addBody('return StateBuilder::from($this)');
		$getState->addBody("\t->version(1)");

		$fromState = $classType->addMethod('_fromState');
		$fromState->setVisibility('public');
		$fromState->setStatic(TRUE);
		$fromState->setReturnType('self');
		$fromState->addParameter('state')->setType(State::class);
		$fromState->addBody('$state->ensureVersion(1);');
		$fromState->addBody('$self = $state->makeAnEmptyObject(self::class);');
		$fromState->addBody("\assert(\$self instanceof self);\n");

		foreach ($classType->getProperties() as $property) {
			$propertyName = $property->getName();

			// add Stateful::_getState()
			$getState->addBody("\t->field(?, \$this->?)", [
				$propertyName,
				$propertyName,
			]);

			// add Stateful::_fromState()
			$fromState->addBody('$self->? = $state[?];', [
				$propertyName,
				$propertyName,
			]);
		}

		$getState->addBody("\t->build();");
		$fromState->addBody("\nreturn \$self;");
	}

}
