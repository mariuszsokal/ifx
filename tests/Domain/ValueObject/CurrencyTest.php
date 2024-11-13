<?php

namespace Tests\Domain\ValueObject;

use App\Domain\Exception\DomainException;
use App\Domain\ValueObject\Currency;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
	public function testValidCurrencyCode(): void
	{
		$currency = new Currency('USD');
		static::assertEquals('USD', $currency->getCode());
	}

	public function testCurrencyEquality(): void
	{
		$currency1 = new Currency('USD');
		$currency2 = new Currency('USD');
		$currency3 = new Currency('EUR');

		static::assertTrue($currency1->equals($currency2));
		static::assertFalse($currency1->equals($currency3));
	}

	#[DataProvider('invalidCurrencyCodesProvider')]
	public function testInvalidCurrencyCode(string $code): void
	{
		$this->expectException(DomainException::class);
		new Currency($code);
	}

	public static function invalidCurrencyCodesProvider(): array
	{
		return [
			['US'],
			['USDEUR'],
			['123'],
			['usd'],
			['!@#'],
		];
	}
}
