<?php declare(strict_types = 1);

namespace Grifart\ClassScaffolder;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;


final class ClassInNamespace
{

	private function __construct(
		private PhpNamespace $namespace,
		private ClassType $classType,
	) {}


	public static function from(PhpNamespace $namespace, ClassType $classType): self
	{
		return new self($namespace, $classType);
	}

	public static function fromDefinition(ClassDefinition $definition): self
	{
		return new self(
			($namespace = new PhpNamespace($definition->getNamespaceName() ?? '')),
			$namespace->addClass($definition->getClassName()),
		);
	}


	public function getNamespace(): PhpNamespace
	{
		return $this->namespace;
	}


	public function getClassType(): ClassType
	{
		return $this->classType;
	}

}
