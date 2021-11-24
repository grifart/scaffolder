<?php

declare(strict_types=1);

namespace RootNamespace\SubNamespace;

use Iterator;

final class ClassName
{
	/**
	 * @param Iterator<string, int[]> $field2
	 */
	public function __construct(
		private ?string $field1,
		private Iterator $field2,
	) {
	}
}
