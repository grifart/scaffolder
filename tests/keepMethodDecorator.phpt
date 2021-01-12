<?php

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';
$generator = new \Grifart\ClassScaffolder\ClassGenerator();

$codeGenerated = $generator->generateClass(
	new ClassDefinition(
		'Grifart\ClassScaffolder\Test\Stub',
		'StubKeepMethod',
		[],
		['field'=>Types\resolve('mixed')],
		[
			new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('newMethod'),
			new \Grifart\ClassScaffolder\Decorators\KeepMethodDecorator('somethingToBeKept'),
		]
	)
);

Assert::matchFile(__DIR__ . '/Stub/StubKeepMethod.php.expected', (string) $codeGenerated);