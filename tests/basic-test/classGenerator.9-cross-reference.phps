<?php

declare(strict_types=1);

namespace NS;

final class CLS
{
	private SubCLS $field;


	public function __construct(SubCLS $field)
	{
		$this->field = $field;
	}


	public function getField(): SubCLS
	{
		return $this->field;
	}
}
