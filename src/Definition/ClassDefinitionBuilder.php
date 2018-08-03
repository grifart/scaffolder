<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\Types;


final class ClassDefinitionBuilder
{

	/** @var ?string */
	private $namespaceName;

	/** @var string */
	private $className;

	/** @var string[] */
	private $implements = [];

	/** @var Types\Type[] */
	private $fields = [];

	/** @var ClassDecorator[] */
	private $decorators = [];


	public function __construct(string $className)
	{
		$className = \trim($className, '\\');
		$pos = \strrpos($className, '\\');
		if ($pos !== FALSE) {
			$this->namespaceName = \substr($className, 0, $pos);
			$this->className = \substr($className, $pos + 1);

		} else {
			$this->className = $className;
		}
	}


	public function implement(string $implements): self
	{
		if ( ! \interface_exists($implements)) {
			throw new \InvalidArgumentException(\sprintf(
				'Interface %s not found. Make sure your autoloading setup is correct.',
				$implements
			));
		}

		$this->implements[] = $implements;
		return $this;
	}


	public function field(string $name, $type): self
	{
		$this->fields[$name] = Types\resolve($type);
		return $this;
	}


	public function decorate(ClassDecorator $decorator): self
	{
		$this->decorators[] = $decorator;
		return $this;
	}


	public function build(): ClassDefinition
	{
		return new ClassDefinition(
			$this->namespaceName,
			$this->className,
			$this->implements,
			$this->fields,
			$this->decorators
		);
	}

}
