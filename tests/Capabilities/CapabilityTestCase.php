<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;
use Tester\TestCase;
use function Grifart\ClassScaffolder\Definition\definitionOf;

abstract class CapabilityTestCase extends TestCase
{
	/**
	 * @return Capability[]
	 */
	abstract protected function getCapabilities(): array;

	protected function createDefinition(): ClassDefinition
	{
		return definitionOf('RootNamespace\SubNamespace\ClassName')
			->withField('field1', Types\nullable('string'))
			->withField('field2', Types\collection(\Iterator::class, 'string', Types\listOf('int')));
	}

	public function testCapability(): void
	{
		$definition = $this->createDefinition()
			->with(...$this->getCapabilities());

		$expectedFileName = (new \ReflectionClass($this))->getShortName() . '.expected.phps';

		$generator = new ClassGenerator();
		$phpFile = $generator->generateClass($definition);
		$code = (string) $phpFile;

		Assert::matchFile($expectedFileName, $code);
	}
}
