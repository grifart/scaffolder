<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

/**
 * ⚠ Note that for transferring use statements you should use {@see preservedUseStatements()} as well.
 * Call it before this one.
 */
final class PreservedMethod implements Capability
{
	public function __construct(
		private string $methodName,
	) {}

	public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current,): void
	{
		$classToBeGenerated = $draft->getClassType();

		// method already exists, just transfer it to new class
		if ($current !== null && $current->getClassType()->hasMethod($this->methodName)) {
			$keptMethod = $current->getClassType()->getMethod($this->methodName);

			$classToBeGenerated->setMethods([
				...\array_values($classToBeGenerated->getMethods()),
				$keptMethod,
			]);
			return;
		}

		// method does not exist or no previous class
		$addMethodStub = function(ClassType $classToBeGenerated): Method {
			$method = $classToBeGenerated->addMethod($this->methodName);
			$method->setReturnType('void');
			$method->setBody('// Implement method here');
			return $method;
		};

		$methodToBeKept = $classToBeGenerated->hasMethod($this->methodName)
			? $classToBeGenerated->getMethod($this->methodName)
			: $addMethodStub($classToBeGenerated);
		$methodToBeKept->setComment(
			'This method is kept while scaffolding.' . "\n" .
			$methodToBeKept->getComment()
		);
	}
}
