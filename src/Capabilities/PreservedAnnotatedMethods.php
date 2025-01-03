<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\KeepMethod;
use Grifart\ClassScaffolder\Preserve;
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
				if ($attribute->getName() === Preserve::class) {
					self::transferMethod($draft, $existingMethod);
					break; // continue to next method
				}

				if ($attribute->getName() === KeepMethod::class) {
					self::transferMethod($draft, $existingMethod, KeepMethod::class);
					break; // continue to next method
				}
			}
		}
	}

	/**
	 * @param string|null $nonStandardPreservedUseStatement temporary, can be removed with end of support for KeepMethod
	 */
	private static function transferMethod(ClassInNamespace $draft, Method $methodToBeTransferred, ?string $nonStandardPreservedUseStatement = null): void
	{
		$draft->getNamespace()->addUse($nonStandardPreservedUseStatement !== null ?
			$nonStandardPreservedUseStatement :
			Preserve::class);

		$targetClass = $draft->getClassType();
		$targetClass->setMethods([
			...\array_values($targetClass->getMethods()),
			$methodToBeTransferred,
		]);
	}
}
