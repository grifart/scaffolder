<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;


function resolve($type): Type {
	if ($type instanceof Type) {
		return $type;
	}

	if (\in_array($type, ['string', 'int', 'float', 'bool', 'array', 'iterable', 'callable', 'object'], TRUE)) {
		return SimpleType::$type();
	}

	if (\class_exists($type) || \interface_exists($type)) {
		return new ClassType($type);
	}

	throw new \InvalidArgumentException(\sprintf(
		'Cannot resolve type %s.',
		$type
	));
}


function nullable($type): NullableType {
	return new NullableType(
		resolve($type)
	);
}


function collection($collectionType, $keyType, $elementType): CollectionType {
	return new CollectionType(
		resolve($collectionType),
		resolve($keyType),
		resolve($elementType)
	);
}



function listOf($elementType): CollectionType {
	return collection('array', 'int', $elementType);
}
