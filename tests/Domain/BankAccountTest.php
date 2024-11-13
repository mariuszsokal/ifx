<?php

namespace Tests\Domain;

use App\Domain\BankAccount;
use App\Domain\Exception\DomainException;
use App\Domain\ValueObject\Amount;
use App\Domain\ValueObject\Currency;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BankAccountTest extends TestCase
{
	private Currency $usd;
	private BankAccount $account;

	protected function setUp(): void
	{
		$this->usd = new Currency('USD');
		$this->account = new BankAccount($this->usd);
	}

	public function testInitialBalanceIsZero(): void
	{
		static::assertEquals(0, $this->account->getBalance()->getValue());
	}

	public function testCreditIncreasesBalance(): void
	{
		$this->account->credit(new Amount(100), $this->usd);
		static::assertEquals(100, $this->account->getBalance()->getValue());
	}

	#[DataProvider('conversionProvider')]
	public function testConversion(int $amountInPennies, float $amount): void
	{
		$account = new BankAccount($this->usd);
		$account->credit(new Amount($amountInPennies), $this->usd);
		static::assertEquals($amountInPennies, $account->getBalance()->getValue());
		static::assertEquals($amount, $account->getBalance()->getFloatValue());
	}

	public static function conversionProvider(): array
	{
		return [
			[100, 1],
			[150, 1.5],
		];
	}

	#[DataProvider('invalidCreditProvider')]
	public function testCreditWithDifferentCurrencyThrowsException(Currency $currency): void
	{
		$this->expectException(DomainException::class);
		$this->account->credit(new Amount(100), $currency);
	}

	public static function invalidCreditProvider(): array
	{
		return [
			[new Currency('EUR')],
			[new Currency('GBP')],
		];
	}

	public function testDebitDecreasesBalanceWithFee(): void
	{
		$this->account->credit(new Amount(100), $this->usd);
		$this->account->debit(new Amount(50), $this->usd);

		$expectedBalance = 100 - (50 + 1);
		$expectedBalanceInFloat = (float) $expectedBalance / 100;
		static::assertEquals($expectedBalance, $this->account->getBalance()->getValue());
		static::assertEquals($expectedBalanceInFloat, $this->account->getBalance()->getFloatValue());
	}

	#[DataProvider('invalidDebitProvider')]
	public function testDebitWithDifferentCurrencyThrowsException(Currency $currency): void
	{
		$this->account->credit(new Amount(100), $this->usd);
		$this->expectException(DomainException::class);
		$this->account->debit(new Amount(50), $currency);
	}

	public static function invalidDebitProvider(): array
	{
		return [
			[new Currency('EUR')],
			[new Currency('GBP')],
		];
	}

	public function testDebitThrowsExceptionWhenInsufficientFunds(): void
	{
		$this->account->credit(new Amount(30), $this->usd);
		$this->expectException(DomainException::class);
		$this->account->debit(new Amount(50), $this->usd);
	}

	public function testDailyDebitLimitThrowsExceptionAfterThreeDebits(): void
	{
		$this->account->credit(new Amount(500), $this->usd);

		$this->account->debit(new Amount(10), $this->usd);
		$this->account->debit(new Amount(10), $this->usd);
		$this->account->debit(new Amount(10), $this->usd);

		$this->expectException(DomainException::class);
		$this->account->debit(new Amount(10), $this->usd);
	}
}
