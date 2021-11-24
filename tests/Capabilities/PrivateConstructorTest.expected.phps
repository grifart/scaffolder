<?php

declare(strict_types=1);

namespace RootNamespace\SubNamespace;

use Iterator;

final class ClassName
{
	private ?string $field1;

	/** @var Iterator<string, int[]> */
	private Iterator $field2;


	/**
	 * @param Iterator<string, int[]> $field2
	 */
	private function __construct(?string $field1, Iterator $field2)
	{
		$this->field1 = $field1;
		$this->field2 = $field2;
	}
}
