<?php

declare(strict_types=1);

namespace NS;

final class CLS
{
	private ?string $field;


	public function __construct(?string $field)
	{
		$this->field = $field;
	}


	public function getField(): ?string
	{
		return $this->field;
	}
}
