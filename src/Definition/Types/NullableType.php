<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;


use Nette\PhpGenerator\PhpNamespace;


final class NullableType implements CompositeType
{

	private Type $type;


	public function __construct(Type $type)
	{
		$this->type = $type;
	}


	public function getType(): Type
	{
		return $this->type;
	}


	public function getTypeHint(): string
	{
		return $this->type->getTypeHint();
	}


	public function isNullable(): bool
	{
		return true;
	}


	public function requiresDocComment(): bool
	{
		return $this->type->requiresDocComment();
	}


	public function getDocCommentType(PhpNamespace $namespace): string
	{
		return $this->type->getDocCommentType($namespace) . '|null';
	}


	/**
	 * @return Type[]
	 */
	public function getSubTypes(): array
	{
		return [$this->type];
	}

}
