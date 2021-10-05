<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;


final class ClassDefinition
{

	private ?string $namespaceName = null;

	private string $className;

	/** @var class-string[] */
	private array $implements = [];

	/** @var Field[] */
	private array $fields = [];

	/** @var ClassDecorator[] */
	private array $decorators = [];


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


	/**
	 * @param class-string $interfaceName
	 * @param class-string ...$interfaceNames
	 */
	public function thatImplements(string $interfaceName, string ...$interfaceNames): self
	{
		$allInterfaceNames = [$interfaceName, ...$interfaceNames];

		foreach ($allInterfaceNames as $name) {
			if ( ! \interface_exists($name)) {
				throw new \InvalidArgumentException(
					\sprintf(
						'Interface %s not found. Make sure your autoloading setup is correct.',
						$name
					)
				);
			}
		}

		$copy = clone $this;
		$copy->implements = [
			...$copy->implements,
			...$allInterfaceNames,
		];

		return $copy;
	}


	/**
	 * @return string[]
	 */
	public function getImplements(): array
	{
		return $this->implements;
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


	public function decoratedBy(ClassDecorator $decorator, ClassDecorator ...$decorators): self
	{
		$copy = clone $this;
		$copy->decorators = [
			...$copy->decorators,
			$decorator,
			...$decorators,
		];
		return $copy;
	}


	/**
	 * @return ClassDecorator[]
	 */
	public function getDecorators(): array
	{
		return $this->decorators;
	}

}
