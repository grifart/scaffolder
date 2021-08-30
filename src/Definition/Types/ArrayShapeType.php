<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;

final class ArrayShapeType implements CompositeType
{

	/**
	 * @param array<string, Type> $fields
	 */
	public function __construct(
		private array $fields
	) {}


	public function getSubTypes(): array
	{
		return \array_values($this->fields);
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
		foreach ($this->fields as $name => $type) {
			$fields[] = \sprintf(
				'%s: %s',
				$name,
				$type->getDocCommentType($namespace),
			);
		}

		return \sprintf('array{%s}', \implode(', ', $fields));
	}

}
