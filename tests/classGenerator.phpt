<?php declare(strict_types=1);
/**
 * @testCase
 */

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use \Grifart\ClassScaffolder\Definition\Types;

require_once __DIR__ . '/bootstrap.php';



(new class extends \Tester\TestCase {

	/** @var \Grifart\ClassScaffolder\ClassGenerator */
	private $generator;

	protected function setUp()
	{
		parent::setUp();
		$this->generator = new \Grifart\ClassScaffolder\ClassGenerator();
	}

	private function getDecorators(): array {
		return [
			new \Grifart\ClassScaffolder\Decorators\InitializingConstructorDecorator(),
			new \Grifart\ClassScaffolder\Decorators\GettersDecorator(),
		];
	}

	public function dataProvider_generator():array {
		return [
			[
				'classGenerator.1-simple.phps',
				new ClassDefinition('NS', 'CLS', [], [], $this->getDecorators())
			],
			[
				'classGenerator.2-with-iterator.phps',
				new ClassDefinition('NS', 'CLS', [Iterator::class], [], $this->getDecorators())
			],
			[
				'classGenerator.3-with-field.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\resolve('string')], $this->getDecorators())
			],
			[
				'classGenerator.4-with-field-nullable.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\nullable(Types\resolve('string'))], $this->getDecorators())
			],
			[
				'classGenerator.5-with-list.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\listOf('string')], $this->getDecorators())
			],
			[
				'classGenerator.6-with-complex-collection.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\collection(SplObjectStorage::class,ClassDefinition::class, SplFixedArray::class)], $this->getDecorators())
			],
			[
				'classGenerator.7-setters.phps',
				new ClassDefinition('NS', 'CLS', [], ['poem'=>Types\resolve('string')], [new \Grifart\ClassScaffolder\Decorators\SettersDecorator()])
			]
		];
	}

	/** @dataProvider dataProvider_generator */
	public function testGenerator(string $assertionFile, ClassDefinition $definition) {
		\Tester\Assert::matchFile(
			$assertionFile,
			(string) $this->generator->generateClass($definition)
		);
	}


})->run();
