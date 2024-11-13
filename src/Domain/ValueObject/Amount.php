<?php

namespace App\Domain\ValueObject;

use App\Domain\Exception\DomainException;

class Amount
{
	private int $value;

	public function __construct(int $value)
	{
		if ($value < 0) {
			throw new DomainException("Amount cannot be negative");
		}
		$this->value = $value;
	}

	public function getValue(): int
	{
		return $this->value;
	}

	public function getFloatValue(): float
	{
		return $this->value / 100;
	}

	public function add(Amount $amount): Amount
	{
		return new Amount($this->value + $amount->getValue());
	}

	public function subtract(Amount $amount): Amount
	{
		$newValue = $this->value - $amount->getValue();
		if ($newValue < 0) {
			throw new DomainException("Insufficient funds");
		}
		return new Amount($newValue);
	}
}
