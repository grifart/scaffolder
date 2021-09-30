<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\KeepMethod;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;


/**
 * ⚠ Note that you should use `KeepUseStatementsDecorator` as well
 * for transferring use statements as well. List it before this one.
 */
final class KeepAnnotatedMethodsDecorator implements ClassDecorator
{
	public function decorate(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void
	{
		if ($current === null) {
			return;
		}

		foreach ($current->getClassType()->getMethods() as $existingMethod) {
			foreach ($existingMethod->getAttributes() as $attribute) {
				if ($attribute->getName() === KeepMethod::class) {
					self::transferMethod($draft, $existingMethod);
					break; // continue to next method
				}
			}
		}
	}

	private static function transferMethod(ClassInNamespace $draft, Method $methodToBeTransferred): void
	{
		$draft->getNamespace()->addUse(KeepMethod::class);

		$targetClass = $draft->getClassType();
		$targetClass->setMethods([
			...\array_values($targetClass->getMethods()),
			$methodToBeTransferred,
		]);
	}
}
