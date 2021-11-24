<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

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
	public function withField2(Iterator $field2): self
	{
		$self = clone $this;
		$self->field2 = $field2;
		return $self;
	}
}
