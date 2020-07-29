<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;

final class GenericType implements CompositeType
{

	private Type $baseType;

	/** @var Type[] */
	private array $parameterTypes;


	public function __construct(Type $baseType, Type ...$parameterTypes)
	{
		$this->baseType = $baseType;
		$this->parameterTypes = $parameterTypes;
	}


	public function getBaseType(): Type
	{
		return $this->baseType;
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
		return $this->baseType->getTypeHint();
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
		return \sprintf(
			'%s<%s>',
			$this->baseType->getDocCommentType($namespace),
			\implode(', ', \array_map(
				static fn(Type $type) => $type->getDocCommentType($namespace),
				$this->parameterTypes,
			)),
		);
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
			$this->baseType,
			...$this->parameterTypes,
		];
	}
}
