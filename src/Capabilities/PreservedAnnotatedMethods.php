<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\KeepMethod;
use Nette\PhpGenerator\Method;

/**
 * ⚠ Note that for transferring use statements you should use {@see preservedUseStatements()} as well.
 * Call it before this one.
 */
final class PreservedAnnotatedMethods implements Capability
{
	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
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
