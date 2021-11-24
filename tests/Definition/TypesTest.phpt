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
			typeClass: Types\SimpleType::class,
		);
	}

	public function testNullableType(): void
	{
		$this->assertType(
			Types\nullable('string'),
			'string',
			isNullable: true,
			typeClass: Types\NullableType::class,
		);
	}

	public function testClassType(): void
	{
		$this->assertType(
			Types\resolve(\Iterator::class),
			'Iterator',
			typeClass: Types\CheckedClassType::class,
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
			typeClass: Types\NonCheckedClassType::class,
		);
	}

	public function testDefinitionReferenceType(): void
	{
		$definition = \Grifart\ClassScaffolder\Definition\define(GeneratedClass::class);

		$this->assertType(
			Types\resolve($definition),
			'Grifart\ClassScaffolder\Test\Definition\GeneratedClass',
			typeClass: Types\NonCheckedClassType::class,
		);
	}

	public function testListType(): void
	{
		$this->assertType(
			Types\listOf('string'),
			'array',
			docComment: 'string[]',
			typeClass: Types\ListType::class,
		);
	}

	public function testCollectionType(): void
	{
		$this->assertType(
			Types\collection('iterable', 'int', 'string'),
			'iterable',
			docComment: 'iterable<int, string>',
			typeClass: Types\CollectionType::class,
		);
	}

	public function testGenericType(): void
	{
		$this->assertType(
			Types\generic(\Generator::class, 'int', 'string', 'object', 'mixed'),
			'Generator',
			docComment: 'Generator<int, string, object, mixed>',
			typeClass: Types\GenericType::class,
		);
	}

	public function testUnionType(): void
	{
		$this->assertType(
			Types\union('string', 'int'),
			'string|int',
			typeClass: Types\UnionType::class,
		);

		$this->assertType(
			Types\union(Types\listOf('string'), 'int'),
			'array|int',
			docComment: 'string[]|int',
			typeClass: Types\UnionType::class,
		);
	}

	public function testIntersectionType(): void
	{
		$this->assertType(
			Types\intersection(\Traversable::class, \Countable::class),
			'Traversable&Countable',
			typeClass: Types\IntersectionType::class,
		);

		$this->assertType(
			Types\intersection(Types\generic(\Traversable::class, 'string'), \Countable::class),
			'Traversable&Countable',
			docComment: 'Traversable<string>&Countable',
			typeClass: Types\IntersectionType::class,
		);
	}

	public function testArrayShapeType(): void
	{
		$this->assertType(
			Types\arrayShape(['key1' => 'string', 'key2' => 'int', 'key3?' => 'bool']),
			'array',
			docComment: 'array{key1: string, key2: int, key3?: bool}',
			typeClass: Types\ArrayShapeType::class,
		);
	}

	public function testTupleType(): void
	{
		$this->assertType(
			Types\tuple('string', Types\nullable('int')),
			'array',
			docComment: 'array{string, int|null}',
			typeClass: Types\TupleType::class,
		);
	}

	private function assertType(
		Types\Type $type,
		string $typeHint,
		bool $isNullable = false,
		?string $docComment = null,
		string $typeClass = Types\Type::class,
	): void
	{
		Assert::type($typeClass, $type);
		Assert::same($typeHint, $type->getTypeHint());
		Assert::same($isNullable, $type->isNullable());
		Assert::same($docComment !== null, $type->requiresDocComment());
		if ($docComment !== null) {
			Assert::same($docComment, $type->getDocCommentType(new PhpNamespace('')));
		}
	}
}

(new TypesTest())->run();
