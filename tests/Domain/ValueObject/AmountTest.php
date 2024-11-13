<?php

namespace Tests\Domain\ValueObject;

use App\Domain\Exception\DomainException;
use App\Domain\ValueObject\Amount;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
	public function testAddAmount(): void
	{
		$amount1 = new Amount(50);
		$amount2 = new Amount(30);
		$result = $amount1->add($amount2);

		static::assertEquals(80, $result->getValue());
	}

	public function testSubtractAmount(): void
	{
		$amount1 = new Amount(50);
		$amount2 = new Amount(30);
		$result = $amount1->subtract($amount2);

		static::assertEquals(20, $result->getValue());
	}

	public function testSubtractAmountThrowsExceptionWhenInsufficientFunds(): void
	{
		$this->expectException(DomainException::class);
		$amount1 = new Amount(30);
		$amount2 = new Amount(50);
		$amount1->subtract($amount2);
	}

	#[DataProvider('validAmountProvider')]
	public function testValidAmount(int $value): void
	{
		$amount = new Amount($value);
		static::assertEquals($value, $amount->getValue());
	}

	public static function validAmountProvider(): array
	{
		return [
			[0],
			[100],
			[200],
		];
	}

	#[DataProvider('invalidAmountProvider')]
	public function testNegativeAmountThrowsException(int $value): void
	{
		$this->expectException(DomainException::class);
		new Amount($value);
	}

	public static function invalidAmountProvider(): array
	{
		return [
			[-50],
			[-100],
		];
	}
}
