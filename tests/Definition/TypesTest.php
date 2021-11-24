<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Definition;

use Grifart\ClassScaffolder\Definition\Types;
use Nette\PhpGenerator\PhpNamespace;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class TypesTest extends TestCase
{
	public function testSimpleType(): void
	{
		$this->assertType(
			Types\resolve('string'),
			'string',
		);
	}

	public function testNullableType(): void
	{
		$this->assertType(
			Types\nullable('string'),
			'string',
			isNullable: true,
		);
	}

	public function testClassType(): void
	{
		$this->assertType(
			Types\resolve(\Iterator::class),
			'Iterator',
		);

		Assert::throws(
			fn() => Types\resolve(NonExistentClass::class),
			\InvalidArgumentException::class,
			'Cannot resolve type Grifart\ClassScaffolder\Test\Definition\NonExistentClass.',
		);
	}

	public function testNonCheckedClassType(): void
	{
		$this->assertType(
			Types\classType(NonExistentClass::class),
			'Grifart\ClassScaffolder\Test\Definition\NonExistentClass',
		);
	}

	public function testDefinitionReferenceType(): void
	{
		$definition = \Grifart\ClassScaffolder\Definition\define(GeneratedClass::class);

		$this->assertType(
			Types\resolve($definition),
			'Grifart\ClassScaffolder\Test\Definition\GeneratedClass',
		);
	}

	public function testListType(): void
	{
		$this->assertType(
			Types\listOf('string'),
			'array',
			docComment: 'string[]',
		);
	}

	public function testCollectionType(): void
	{
		$this->assertType(
			Types\collection('iterable', 'int', 'string'),
			'iterable',
			docComment: 'iterable<int, string>',
		);
	}

	public function testGenericType(): void
	{
		$this->assertType(
			Types\generic(\Generator::class, 'int', 'string', 'object', 'mixed'),
			'Generator',
			docComment: 'Generator<int, string, object, mixed>',
		);
	}

	public function testUnionType(): void
	{
		$this->assertType(
			Types\union('string', 'int'),
			'string|int',
		);

		$this->assertType(
			Types\union(Types\listOf('string'), 'int'),
			'array|int',
			docComment: 'string[]|int',
		);
	}

	public function testIntersectionType(): void
	{
		$this->assertType(
			Types\intersection(\Traversable::class, \Countable::class),
			'Traversable&Countable',
		);

		$this->assertType(
			Types\intersection(Types\generic(\Traversable::class, 'string'), \Countable::class),
			'Traversable&Countable',
			docComment: 'Traversable<string>&Countable',
		);
	}

	public function testArrayShapeType(): void
	{
		$this->assertType(
			Types\arrayShape(['key1' => 'string', 'key2' => 'int', 'key3?' => 'bool']),
			'array',
			docComment: 'array{key1: string, key2: int, key3?: bool}',
		);
	}

	private function assertType(
		Types\Type $type,
		string $typeHint,
		bool $isNullable = false,
		?string $docComment = null,
	): void
	{
		Assert::same($typeHint, $type->getTypeHint());
		Assert::same($isNullable, $type->isNullable());
		Assert::same($docComment !== null, $type->requiresDocComment());
		if ($docComment !== null) {
			Assert::same($docComment, $type->getDocCommentType(new PhpNamespace('')));
		}
	}
}

(new TypesTest())->run();
