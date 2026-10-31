<?php

namespace BTCPay\Invoice;

if (!\defined('_PS_VERSION_')) {
	exit;
}

/**
 * Shared checkout and webhook input guards.
 */
class CheckoutGuard
{
	public static function isInvoiceReference(mixed $value): bool
	{
		return \is_string($value) && 1 === \preg_match('/^[A-Za-z0-9]{20}$/', $value);
	}

	public static function amountsMatch(string $a, string $b): bool
	{
		return 0 === \bccomp($a, $b, 8);
	}

	public static function isNonNegativeAmount(string $value): bool
	{
		return 1 === \preg_match('/^\d+(\.\d+)?$/', $value);
	}

	public static function isPositiveAmount(string $value): bool
	{
		return self::isNonNegativeAmount($value) && 0 < \bccomp($value, '0', 8);
	}

	/**
	 * Convert a crypto amount with its rate and round to the currency precision.
	 *
	 * Returns null when either input is malformed or the converted amount is not
	 * positive.
	 */
	public static function convertAmount(string $value, string $rate, int $precision): ?float
	{
		if (false === self::isNonNegativeAmount($value) || false === self::isNonNegativeAmount($rate)) {
			return null;
		}

		$converted = \bcmul($value, $rate, 8);
		if (false === self::isPositiveAmount($converted)) {
			return null;
		}

		return \round((float) $converted, \max(0, $precision));
	}

	public static function canCreateOrder(bool $partial, bool $processing, bool $paidLate, bool $overpaid, bool $settled): bool
	{
		return $partial || $processing || $paidLate || $overpaid || $settled;
	}

	public static function paidTransitionAllowed(bool $protect, bool $orderAlreadyPaid, bool $targetIsPaid): bool
	{
		return !$protect || !$orderAlreadyPaid || $targetIsPaid;
	}
}
