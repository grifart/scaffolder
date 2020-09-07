<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\Types\Type;


final class ClassDefinition
{

	private ?string $namespaceName;

	private string $className;

	/** @var string[] */
	private array $implements;

	/** @var Type[] */
	private array $fields;

	/** @var ClassDecorator[] */
	private array $decorators;


	/**
	 * @param string[] $implements
	 * @param Type[] $fields
	 * @param ClassDecorator[] $decorators
	 */
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


	public function getFullyQualifiedName(): string
	{
		return \trim(
			$this->namespaceName . '\\' . $this->className,
			'\\',
		);
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
