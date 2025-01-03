<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;


use Nette\PhpGenerator\PhpNamespace;


final class CheckedClassType implements Type, ClassType
{

	private string $typeName;


	public function __construct(string $typeName)
	{
		if ( ! \class_exists($typeName) && ! \interface_exists($typeName) && ! \enum_exists($typeName)) {
			throw new \InvalidArgumentException(\sprintf(
				'Class type %s was not found. Make sure your autoloading setup is correct.',
				$typeName
			));
		}

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
