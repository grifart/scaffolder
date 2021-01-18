<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
$generator = new \Grifart\ClassScaffolder\ClassGenerator();

/**
 * @param ClassDecorator[] $decorators
 */
$generateClass = static fn (array $decorators) => $generator->generateClass(
	new ClassDefinition(
		'Grifart\ClassScaffolder\Test\KeepMethodDecorator\Stub',
		'StubKeepMethod',
		[],
		['field'=>Types\resolve('mixed')],
		$decorators
	),
);

// methods are preserved
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.preserved.phps',
	(string) $generateClass([
		new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('newMethod'),
		new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKept'),
		new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKeptWithParam'),
		new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKeptWithImportedUses'),
		new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKeptWithAnnotation'),
	]),
);

// methods are overwritten
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.overwritten.phps',
	(string) $generateClass([]),
);
