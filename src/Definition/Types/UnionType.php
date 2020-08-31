<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;

final class UnionType implements CompositeType
{

	/** @var Type[] */
	private array $parameterTypes;


	public function __construct(Type ...$unionTypes)
	{
		$this->parameterTypes = $unionTypes;
	}


	public function getBaseType(): Type
	{
		return SimpleType::mixed();
	}


	/**
	 * @return Type[]
	 */
	public function getParameterTypes(): array
	{
		return $this->parameterTypes;
	}


	public function getTypeHint(): string
	{
		return '';
	}


	public function isNullable(): bool
	{
		return FALSE;
	}


	public function requiresDocComment(): bool
	{
		return TRUE;
	}


	public function getDocCommentType(PhpNamespace $namespace): string
	{
		return \implode('|', \array_map(
			static fn(Type $type) => $type->getDocCommentType($namespace),
			$this->parameterTypes,
		));
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
		return [
			...$this->parameterTypes,
		];
	}
}
