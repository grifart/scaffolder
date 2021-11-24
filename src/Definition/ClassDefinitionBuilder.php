<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\Definition\Types;
use Grifart\ClassScaffolder\Definition\Types\Type;
use function Grifart\ClassScaffolder\Capabilities\implementedInterface;


/**
 * @deprecated use ClassDefinition directly
 */
final class ClassDefinitionBuilder
{

	/** @var class-string[] */
	private array $implements = [];

	/** @var Field[] */
	private array $fields = [];

	/** @var Capability[] */
	private array $decorators = [];


	public function __construct(
		private string $className,
	)
	{
		\trigger_error('ClassDefinitionBuilder is deprecated, use ClassDefinition directly instead.', \E_USER_DEPRECATED);
	}


	/**
	 * @param class-string $implements
	 */
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


	public function field(string $name, Type|ClassDefinition|string $type): self
	{
		$this->fields[] = new Field(
			$name,
			Types\resolve($type),
		);
		return $this;
	}


	public function decorate(Capability $decorator): self
	{
		$this->decorators[] = $decorator;
		return $this;
	}


	public function build(): ClassDefinition
	{
		$definition = (new ClassDefinition($this->className));

		foreach ($this->fields as $field) {
			$definition = $definition->withField($field->getName(), $field->getType());
		}

		foreach ($this->implements as $implement) {
			$definition = $definition->with(implementedInterface($implement));
		}

		foreach ($this->decorators as $decorator) {
			$definition = $definition->with($decorator);
		}

		return $definition;
	}

}
