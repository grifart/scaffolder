<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;

final class ListType implements CompositeType
{
	private Type $itemType;

	public function __construct(Type $itemType)
	{
		$this->itemType = $itemType;
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
		return \sprintf('%s[]', $this->itemType->getDocCommentType($namespace));
	}

	public function hasComment(): bool
	{
		return false;
	}

	public function getComment(PhpNamespace $namespace): ?string
	{
		return null;
	}

	public function getSubTypes(): array
	{
		return [$this->itemType];
	}
}
