<?php

namespace App\Domain\ValueObject;

use App\Domain\Exception\DomainException;

readonly class Currency
{
	public string $code;

	public function __construct(string $code)
	{
		if (!preg_match('/^[A-Z]{3}$/', $code)) {
			throw new DomainException("Invalid currency code");
		}
		$this->code = $code;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function equals(Currency $currency): bool
	{
		return $this->code === $currency->code;
	}
}
