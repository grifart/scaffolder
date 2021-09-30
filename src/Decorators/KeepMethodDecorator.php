<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Decorators;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\Strings;


final class KeepMethodDecorator implements ClassDecorator
{
	private string $methodToBeKept;

	public function __construct(string $methodName)
	{
		$this->methodToBeKept = $methodName;
	}

	public function decorate(ClassDefinition $definition, ClassInNamespace $draft): void
	{
		$alreadyExistingClass = self::getAlreadyExistingClass($definition);
		$classToBeGenerated = $draft->getClassType();

		// method already exists, just transfer it to new class
		if ($alreadyExistingClass !== null && $alreadyExistingClass->hasMethod($this->methodToBeKept)) {
			$keptMethod = $alreadyExistingClass->getMethod($this->methodToBeKept);

			self::addClassesUsedInMethodToUses($keptMethod, $draft->getNamespace());

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
		 * Otherwise e.g. fromDTO(\Path\To\DTO $dto) would be generated instead of fromDTO(DTO $dto)
		 */
		foreach ($method->getParameters() as $parameter) {
			$type = $parameter->getType();
			if ($type !== null && (\class_exists($type) || \interface_exists($type))) {
				$namespace->addUse($type);
			}
		}

		/*
		 * Body class usages.
		 * Otherwise e.g. \Path\To\CampaignRole::ROLE_MANAGER would be generated instead of CampaignRole::ROLE_MANAGER
		 */
		$body = $method->getBody();
		if ($body === null) {
			return;
		}

		$usedClasses = Strings::matchAll($body, '/(\\\\([\\\\\w]+))/'); // search for \A\B\C
		foreach ($usedClasses as $match) {
			$usedClass = $match[2]; // A\B\C
			if (\class_exists($usedClass)) {
				$namespace->addUse($usedClass);

				$a = \explode('\\', $usedClass);
				$b = \array_pop($a); // C
				\assert($b !== null, 'Array can not be empty.');
				$body = \str_replace($match[1], $b, $body); // \A\B\C -> C
			}
		}

		$method->setBody($body);
	}
}
