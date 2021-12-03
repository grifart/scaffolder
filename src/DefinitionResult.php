<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder;

use Grifart\ClassScaffolder\Definition\ClassDefinition;

/**
 * @internal
 */
final class DefinitionResult
{
	private function __construct(
		private ClassDefinition $definition,
		private \Throwable|null $error,
	) {}

	public static function success(ClassDefinition $definition): self
	{
		return new self($definition, null);
	}

	public static function error(ClassDefinition $definition, \Throwable $error): self
	{
		return new self($definition, $error);
	}

	public function getClassName(): string
	{
		return $this->definition->getFullyQualifiedName();
	}

	public function isSuccessful(): bool
	{
		return $this->error === null;
	}

	public function getError(): \Throwable|null
	{
		return $this->error;
	}
}
