<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

function constructorWithPromotedProperties(): ConstructorWithPromotedProperties {
	return new ConstructorWithPromotedProperties();
}

function getters(): Getters {
	return new Getters();
}

/**
 * @param string ...$fieldNames
 */
function immutableSetters(string ...$fieldNames): ImmutableSetters {
	return new ImmutableSetters(...$fieldNames);
}

/**
 * @param class-string $interfaceName
 */
function implementedInterface(string $interfaceName): ImplementedInterface {
	return new ImplementedInterface($interfaceName);
}

function initializingConstructor(): InitializingConstructor {
	return new InitializingConstructor();
}

function namedConstructor(string $constructorName): NamedConstructor {
	return new NamedConstructor($constructorName);
}

function preservedAnnotatedMethods(): PreservedAnnotatedMethods {
	return new PreservedAnnotatedMethods();
}

function preservedMethod(string $methodName): PreservedMethod {
	return new PreservedMethod($methodName);
}

function preservedUseStatements(): PreservedUseStatements {
	return new PreservedUseStatements();
}

function privatizedConstructor(): PrivatizedConstructor {
	return new PrivatizedConstructor();
}

function properties(): Properties {
	return new Properties();
}

function readonlyClass(): ReadonlyClass {
	return new ReadonlyClass();
}

function readonlyProperties(): ReadonlyProperties {
	return new ReadonlyProperties();
}

function setters(): Setters {
	return new Setters();
}
