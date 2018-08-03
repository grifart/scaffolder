<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\Types\Type;


final class ClassDefinition
{

	/** @var ?string */
	private $namespaceName;

	/** @var string */
	private $className;

	/** @var string[] */
	private $implements;

	/** @var Type[] */
	private $fields;

	/** @var ClassDecorator[] */
	private $decorators;


	public function __construct(
		?string $namespaceName,
		string $className,
		array $implements,
		array $fields,
		array $decorators
	)
	{
		$this->namespaceName = $namespaceName;
		$this->className = $className;
		$this->implements = $implements;
		$this->fields = $fields;
		$this->decorators = $decorators;
	}


	public function getNamespaceName(): ?string
	{
		return $this->namespaceName;
	}


	public function getClassName(): string
	{
		return $this->className;
	}


	/**
	 * @return string[]
	 */
	public function getImplements(): array
	{
		return $this->implements;
	}


	/**
	 * @return Type[]
	 */
	public function getFields(): array
	{
		return $this->fields;
	}


	/**
	 * @return ClassDecorator[]
	 */
	public function getDecorators(): array
	{
		return $this->decorators;
	}

}
