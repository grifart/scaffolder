<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';
$generator = new \Grifart\ClassScaffolder\ClassGenerator();

$generateClass = fn (bool $withDecorators) => $generator->generateClass(
	new ClassDefinition(
		'Grifart\ClassScaffolder\Test\Stub',
		'StubKeepMethod',
		[],
		['field'=>Types\resolve('mixed')],
		$withDecorators
			? [
				new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('newMethod'),
				new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKept'),
				new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKeptWithParam'),
				new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKeptWithImportedUses'),
				new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('methodToBeKeptWithAnnotation'),
			]
			: [],
	),
);

// methods are preserved
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.preserved.phps',
	(string) $generateClass(withDecorators: true),
);

// methods are overwritten
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.overwritten.phps',
	(string) $generateClass(withDecorators: false),
);
