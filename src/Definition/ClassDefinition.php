<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

use Grifart\ClassScaffolder\Capabilities\Capability;

final class ClassDefinition
{

	private ?string $namespaceName = null;

	private string $className;

	/** @var Field[] */
	private array $fields = [];

	/** @var Capability[] */
	private array $capabilities = [];


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


	public function withField(string $name, Types\Type|self|string $type): self
	{
		$copy = clone $this;
		$copy->fields[] = new Field($name, Types\resolve($type));
		return $copy;
	}


	/**
	 * @param array<string, Types\Type|self|string> $fields
	 */
	public function withFields(array $fields): self
	{
		$copy = clone $this;
		foreach ($fields as $name => $type) {
			$copy->fields[] = new Field($name, Types\resolve($type));
		}

		return $copy;
	}


	/**
	 * @return Field[]
	 */
	public function getFields(): array
	{
		return $this->fields;
	}


	public function with(Capability $capability, Capability ...$capabilities): self
	{
		$copy = clone $this;
		$copy->capabilities = [
			...$copy->capabilities,
			$capability,
			...$capabilities,
		];
		return $copy;
	}


	/**
	 * @return Capability[]
	 */
	public function getCapabilities(): array
	{
		return $this->capabilities;
	}

}
