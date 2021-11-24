<?php

declare(strict_types=1);

namespace NS;

final class CLS
{
	private mixed $field;


	public function __construct(mixed $field)
	{
		$this->field = $field;
	}


	public function getField(): mixed
	{
		return $this->field;
	}
}
