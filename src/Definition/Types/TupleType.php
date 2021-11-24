<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;

final class TupleType implements CompositeType
{
	/** @var Type[]  */
	private array $types;

	public function __construct(Type ...$types)
	{
		$this->types = $types;
	}

	public function getSubTypes(): array
	{
		return $this->types;
	}

	public function getTypeHint(): string
	{
		return 'array';
	}

	public function isNullable(): bool
	{
		return false;
	}

	public function requiresDocComment(): bool
	{
		return true;
	}

	public function getDocCommentType(PhpNamespace $namespace): string
	{
		$fields = [];
		foreach ($this->types as $type) {
			$fields[] = $type->getDocCommentType($namespace);
		}

		return \sprintf('array{%s}', \implode(', ', $fields));
	}
}
