<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;

final class IntersectionType implements CompositeType
{

	/** @var Type[] */
	private array $subTypes;


	public function __construct(Type $first, Type $second, Type ...$rest)
	{
		$this->subTypes = [$first, $second, ...$rest];
	}


	/**
	 * @return Type[]
	 */
	public function getSubTypes(): array
	{
		return $this->subTypes;
	}


	public function getTypeHint(): string
	{
		return \implode('&', \array_map(
			static fn(Type $type) => $type->getTypeHint(),
			$this->subTypes,
		));
	}


	public function isNullable(): bool
	{
		return false;
	}


	public function requiresDocComment(): bool
	{
		foreach ($this->subTypes as $subType) {
			if ($subType->requiresDocComment()) {
				return true;
			}
		}

		return false;
	}


	public function getDocCommentType(PhpNamespace $namespace): string
	{
		return \implode('&', \array_map(
			static fn(Type $type) => $type->getDocCommentType($namespace),
			$this->subTypes,
		));
	}

}
