<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;


final class CollectionType implements CompositeType
{

	private Type $collectionType;

	private Type $keyType;

	private Type $elementType;


	public function __construct(Type $collectionType, Type $keyType, Type $elementType)
	{
		$this->collectionType = $collectionType;
		$this->keyType = $keyType;
		$this->elementType = $elementType;
	}


	public function getCollectionType(): Type
	{
		return $this->collectionType;
	}


	public function getKeyType(): Type
	{
		return $this->keyType;
	}


	public function getElementType(): Type
	{
		return $this->elementType;
	}


	public function getTypeHint(): string
	{
		return $this->collectionType->getTypeHint();
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
			'%s<%s, %s>',
			$this->collectionType->getDocCommentType($namespace),
			$this->keyType->getDocCommentType($namespace),
			$this->elementType->getDocCommentType($namespace),
		);
	}


	/**
	 * @return Type[]
	 */
	public function getSubTypes(): array
	{
		return [
			$this->collectionType,
			$this->keyType,
			$this->elementType,
		];
	}
}
