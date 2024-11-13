<?php

namespace App\Domain;

use App\Domain\Exception\DomainException;
use App\Domain\ValueObject\Amount;
use App\Domain\ValueObject\Currency;

class BankAccount
{
	private const TRANSACTION_FEE_PERCENT = 0.005;
	private const DAILY_DEBIT_LIMIT = 3;

	public readonly Currency $currency;
	private Amount $balance;
	private array $debitDates = [];

	public function __construct(Currency $currency)
	{
		$this->currency = $currency;
		$this->balance = new Amount(0);
	}

	public function getBalance(): Amount
	{
		return $this->balance;
	}

	public function credit(Amount $amount, Currency $currency): void
	{
		if (!$this->currency->equals($currency)) {
			throw new DomainException("Currency mismatch for credit operation");
		}

		$this->balance = $this->balance->add($amount);
	}

	public function debit(Amount $amount, Currency $currency): void
	{
		if (!$this->currency->equals($currency)) {
			throw new DomainException("Currency mismatch for debit operation");
		}

		$transactionFeeValue = (int) ceil($amount->getValue() * self::TRANSACTION_FEE_PERCENT);
		$transactionFee = new Amount($transactionFeeValue);
		$totalAmount = $amount->add($transactionFee);

		if ($this->balance->getValue() < $totalAmount->getValue()) {
			throw new DomainException("Insufficient funds for debit operation");
		}

		$today = new \DateTime('now');
		$todayDebits = array_filter($this->debitDates, fn($date) => $date->format('Y-m-d') === $today->format('Y-m-d'));

		if (count($todayDebits) >= self::DAILY_DEBIT_LIMIT) {
			throw new DomainException("Daily debit transaction limit reached");
		}

		$this->debitDates[] = $today;
		$this->balance = $this->balance->subtract($totalAmount);
	}
}
