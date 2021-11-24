<?php

declare(strict_types=1);

namespace RootNamespace\SubNamespace;

use Iterator;

final class ClassName
{
	private ?string $field1;

	/** @var Iterator<string, int[]> */
	private Iterator $field2;


	public function setField1(?string $field1): void
	{
		$this->field1 = $field1;
	}


	/**
	 * @param Iterator<string, int[]> $field2
	 */
	public function setField2(Iterator $field2): void
	{
		$this->field2 = $field2;
	}
}
