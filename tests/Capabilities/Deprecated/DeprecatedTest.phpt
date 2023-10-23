<?php declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Test\Capabilities\Deprecated;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Tester\Assert;
use Tester\TestCase;
use function Grifart\ClassScaffolder\Capabilities\deprecated;
use function Grifart\ClassScaffolder\Definition\definitionOf;


require __DIR__ . '/../../bootstrap.php';

final class DeprecatedTest extends TestCase
{
	private function doAssert(
		ClassDefinition $definition,
		string $expectedFilePath,
	): void
	{
		$generator = new ClassGenerator();
		$phpFile = $generator->generateClass($definition);
		$code = (string) $phpFile;

		Assert::matchFile($expectedFilePath, $code);
	}

	public function testWithoutParams(): void
	{
		$this->doAssert(
			definition: definitionOf('ClassName')
				->with(deprecated()),
			expectedFilePath: __DIR__ . '/DeprecatedTest.expected.onlyAnnotation.phps',
		);
	}

	public function testWithDescription(): void
	{
		$this->doAssert(
			definition: definitionOf('ClassName')
				->with(deprecated(description: 'will be removed in v4.0.0')),
			expectedFilePath: __DIR__ . '/DeprecatedTest.expected.withDescription.phps',
		);
	}

	public function testWithReplacement(): void
	{
		$this->doAssert(
			definition: definitionOf('ClassName')
				->with(deprecated(replacement: Replacement::class)),
			expectedFilePath: __DIR__ . '/DeprecatedTest.expected.withReplacement.phps',
		);
	}

	public function testWithAll(): void
	{
		$this->doAssert(
			definition: definitionOf('ClassName')
				->with(deprecated(
					description: 'will be removed in v4.0.0',
					replacement: Replacement::class,
				)),
			expectedFilePath: __DIR__ . '/DeprecatedTest.expected.withAll.phps',
		);
	}
}

(new DeprecatedTest())->run();
