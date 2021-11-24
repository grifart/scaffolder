<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\KeepMethodDecorator\Stub;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;

final class StubKeepMethod
{
	/**
	 * This method is kept while scaffolding.
	 */
	public function newMethod(): void
	{
		// Implement method here
	}


	public function methodToBeKept(): mixed
	{
		return 'whatever';
	}


	public function methodToBeKeptWithParam(int $whatever): void
	{
	}


	public function methodToBeKeptWithMixedParam(mixed $whatever): void
	{
	}


	public function methodToBeKeptWithImportedUses(ClassDefinitionBuilder $builder): string
	{
		return ClassDefinition::class;
	}


	/**
	 * Php doc!
	 * @return void
	 * @throws \Throwable
	 */
	public function methodToBeKeptWithAnnotation(): void
	{
	}
}
