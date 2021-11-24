<?php

declare(strict_types=1);

namespace NS;

final class CLS
{
	public function __construct(private string $field)
	{
	}


	public function getField(): string
	{
		return $this->field;
	}
}
