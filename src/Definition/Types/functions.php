<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Grifart\ClassScaffolder\Definition\ClassDefinition;

function resolve(Type|ClassDefinition|string $type): Type {
	if ($type instanceof Type) {
		return $type;
	}

	if ($type instanceof ClassDefinition) {
		return new NonCheckedClassType($type->getFullyQualifiedName());
	}

	if (\is_string($type) && $type[0] === '?') {
		return nullable(resolve(\substr($type, 1)));
	}

	if (\in_array($type, ['string', 'int', 'float', 'bool', 'false', 'array', 'iterable', 'callable', 'object', 'mixed', 'null'], TRUE)) {
		return SimpleType::$type();
	}

	if (\class_exists($type) || \interface_exists($type) || (\PHP_VERSION_ID >= 80100 && \enum_exists($type))) {
		return new CheckedClassType($type);
	}

	throw new \InvalidArgumentException(\sprintf(
		'Cannot resolve type %s.',
		$type
	));
}


function classType(string $type): NonCheckedClassType {
	return new NonCheckedClassType($type);
}


function nullable(Type|ClassDefinition|string $type): NullableType {
	return new NullableType(
		resolve($type)
	);
}


function generic(Type|ClassDefinition|string $baseType, Type|ClassDefinition|string ...$parameterTypes): GenericType {
	return new GenericType(
		resolve($baseType),
		...\array_map('\Grifart\ClassScaffolder\Definition\Types\resolve', $parameterTypes),
	);
}


function collection(Type|ClassDefinition|string $collectionType, Type|ClassDefinition|string $keyType, Type|ClassDefinition|string $elementType): CollectionType {
	return new CollectionType(
		resolve($collectionType),
		resolve($keyType),
		resolve($elementType)
	);
}

function listOf(Type|ClassDefinition|string $elementType): ListType {
	return new ListType(resolve($elementType));
}

/**
 * @param array<string, Type|ClassDefinition|string> $shape
 */
function arrayShape(array $shape): ArrayShapeType {
	return new ArrayShapeType(\array_map('\Grifart\ClassScaffolder\Definition\Types\resolve', $shape));
}


function union(
	Type|ClassDefinition|string $first,
	Type|ClassDefinition|string $second,
	Type|ClassDefinition|string ...$rest,
): UnionType {
	return new UnionType(
		resolve($first),
		resolve($second),
		...\array_map('\Grifart\ClassScaffolder\Definition\Types\resolve', $rest),
	);
}


function intersection(
	Type|ClassDefinition|string $first,
	Type|ClassDefinition|string $second,
	Type|ClassDefinition|string ...$rest,
): IntersectionType {
	return new IntersectionType(
		resolve($first),
		resolve($second),
		...\array_map('\Grifart\ClassScaffolder\Definition\Types\resolve', $rest),
	);
}
