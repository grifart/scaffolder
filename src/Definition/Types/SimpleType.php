<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;


use Nette\PhpGenerator\PhpNamespace;


final class SimpleType implements Type
{

	private string $type;


	private function __construct(string $type)
	{
		$this->type = $type;
	}


	public static function string(): self
	{
		return new self('string');
	}


	public static function int(): self
	{
		return new self('int');
	}


	public static function float(): self
	{
		return new self('float');
	}


	public static function bool(): self
	{
		return new self('bool');
	}


	public static function array(): self
	{
		return new self('array');
	}


	public static function iterable(): self
	{
		return new self('iterable');
	}


	public static function callable(): self
	{
		return new self('callable');
	}


	public static function object(): self
	{
		return new self('object');
	}


	public static function mixed(): self
	{
		return new self('mixed');
	}

	public function getTypeHint(): string
	{
		return $this->type;
	}


	public function isNullable(): bool
	{
		return FALSE;
	}


	public function requiresDocComment(): bool
	{
		return FALSE;
	}


	public function getDocCommentType(PhpNamespace $namespace): string
	{
		return $this->type;
	}


	public function hasComment(): bool
	{
		return FALSE;
	}


	public function getComment(PhpNamespace $namespace): ?string
	{
		return NULL;
	}

}
