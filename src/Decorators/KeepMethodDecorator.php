<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;


/**
 * ⚠ Note that you should use `KeepUseStatementsDecorator` as well
 * for transferring use statements as well. List it before this one.
 */
final class KeepMethodDecorator implements ClassDecorator
{

	public function __construct(
		private string $methodToBeKept,
	) {}

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		$classToBeGenerated = $draft->getClassType();

		// method already exists, just transfer it to new class
		if ($current !== null && $current->getClassType()->hasMethod($this->methodToBeKept)) {
			$keptMethod = $current->getClassType()->getMethod($this->methodToBeKept);

			$classToBeGenerated->setMethods([
				...\array_values($classToBeGenerated->getMethods()),
				$keptMethod,
			]);
			return;
		}

		// method does not exist or no previous class
		$addMethodStub = function(ClassType $classToBeGenerated): Method {
			$method = $classToBeGenerated->addMethod($this->methodToBeKept);
			$method->setReturnType('void');
			$method->setBody('// Implement method here');
			return $method;
		};

		$methodToBeKept = $classToBeGenerated->hasMethod($this->methodToBeKept)
			? $classToBeGenerated->getMethod($this->methodToBeKept)
			: $addMethodStub($classToBeGenerated);
		$methodToBeKept->setComment(
			'This method is kept while scaffolding.' . "\n" .
			$methodToBeKept->getComment()
		);
	}
}
