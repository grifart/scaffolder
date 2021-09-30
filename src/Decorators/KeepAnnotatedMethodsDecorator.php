<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\KeepMethod;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\Strings;


final class KeepAnnotatedMethodsDecorator implements ClassDecorator
{
	public function decorate(ClassInNamespace $classInNamespace, ClassDefinition $definition): void
	{
		$alreadyExistingClass = self::getAlreadyExistingClass($definition);
		if ($alreadyExistingClass === null) {
			return;
		}

		foreach ($alreadyExistingClass->getMethods() as $existingMethod) {
			foreach ($existingMethod->getAttributes() as $attribute) {
				if ($attribute->getName() === KeepMethod::class) {
					self::transferMethod($classInNamespace->getNamespace(), $classInNamespace->getClassType(), $existingMethod);
					break; // continue to next method
				}
			}
		}
	}

	private static function transferMethod(PhpNamespace $targetClassNamespace, ClassType $targetClass, Method $methodToBeTransferred): void
	{
		$targetClassNamespace->addUse(KeepMethod::class);
		self::addClassesUsedInMethodToUses($methodToBeTransferred, $targetClassNamespace);

		$targetClass->setMethods([
			...\array_values($targetClass->getMethods()),
			$methodToBeTransferred,
		]);
	}

	private static function getAlreadyExistingClass(ClassDefinition $definition): ?ClassType
	{
		$namespace = $definition->getNamespaceName();
		$classFqn = ($namespace === null ? '' : $namespace) . '\\' . $definition->getClassName();

		if ( ! \class_exists($classFqn)) {
			return null;
		}

		return ClassType::withBodiesFrom($classFqn);
	}


	/*
	 * Converts FQNs in parameters and method body to class name only.
	 */
	private static function addClassesUsedInMethodToUses(Method $method, PhpNamespace $namespace): void
	{
		/*
		 * Parameters.
		 * So that `fromDTO(\Path\To\DTO $dto)` becomes `fromDTO(DTO $dto)`
		 */
		foreach ($method->getParameters() as $parameter) {
			$type = $parameter->getType();
			if ($type !== null && (\class_exists($type) || \interface_exists($type))) {
				$namespace->addUse($type);
			}
		}

		/*
		 * Body class usages.
		 * So that `\Path\To\CampaignRole::ROLE_MANAGER` becomes `CampaignRole::ROLE_MANAGER`
		 */
		$body = $method->getBody();
		if ($body === null) {
			return;
		}

		// find all FQN classes
		$usedClasses = Strings::matchAll($body, '/(\\\\([\\\\\w]+))/'); // search for \A\B\C
		foreach ($usedClasses as $match) {
			$usedClass = $match[2]; // A\B\C
			if (\class_exists($usedClass)) {
				// add to uses
				$namespace->addUse($usedClass);

				// replace FQN with just class name
				$a = \explode('\\', $usedClass);
				$b = \array_pop($a); // C
				\assert($b !== null, 'Array can not be empty.');
				$body = \str_replace($match[1], $b, $body); // \A\B\C -> C
			}
		}

		$method->setBody($body);
	}
}
