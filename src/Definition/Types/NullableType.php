<?php

declare(strict_types = 1);

namespace Doklady\Scaffolder\Definition\Types;


use Nette\PhpGenerator\PhpNamespace;


final class NullableType implements CompositeType
{

	/**
	 * @var Type
	 */
	private $type;


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
		return TRUE;
	}


	public function requiresDocComment(): bool
	{
		return FALSE;
	}


	public function getDocCommentType(PhpNamespace $namespace): string
	{
		return $this->type->getDocCommentType($namespace) . '|null';
	}


	public function hasComment(): bool
	{
		return FALSE;
	}


	public function getComment(PhpNamespace $namespace): ?string
	{
		return NULL;
	}


	/**
	 * @return Type[]
	 */
	public function getSubTypes(): array
	{
		return [$this->type];
	}

}
