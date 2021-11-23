<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

function constructorWithPromotedProperties(): ConstructorWithPromotedProperties {
	return new ConstructorWithPromotedProperties();
}

function getters(): Getters {
	return new Getters();
}

function initializingConstructor(): InitializingConstructor {
	return new InitializingConstructor();
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

function properties(): Properties {
	return new Properties();
}

function readonlyProperties(): ReadonlyProperties {
	return new ReadonlyProperties();
}

function setters(): Setters {
	return new Setters();
}

function statefulImplementation(): StatefulImplementation {
	return new StatefulImplementation();
}
