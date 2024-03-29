<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;


use Nette\PhpGenerator\PhpNamespace;


final class NonCheckedClassType implements Type, ClassType
{

	private string $typeName;


	public function __construct(string $typeName)
	{
		$this->typeName = $typeName;
	}


	public function getTypeName(): string
	{
		return $this->typeName;
	}


	public function getTypeHint(): string
	{
		return $this->typeName;
	}


	public function isNullable(): bool
	{
		return FALSE;
	}


	public function getDocCommentType(PhpNamespace $namespace): string
	{
		return $namespace->simplifyName($this->typeName);
	}


	public function requiresDocComment(): bool
	{
		return FALSE;
	}

}
