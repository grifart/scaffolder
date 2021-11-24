<?php

declare(strict_types=1);

namespace RootNamespace\SubNamespace;

use Iterator;

final class ClassName
{
	private ?string $field1;

	/** @var Iterator<string, int[]> */
	private Iterator $field2;
}
