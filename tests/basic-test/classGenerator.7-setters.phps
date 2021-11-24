<?php

declare(strict_types=1);

namespace NS;

final class CLS
{
	private ?string $poem;


	public function setPoem(?string $poem): void
	{
		$this->poem = $poem;
	}
}
