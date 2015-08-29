<?php

/**
 * @file
 * Contains \Commercie\Money\Amount.
 */

namespace Commercie\Money;

use Commercie\Currency\CurrencyInterface;

/**
 * Provides an amount of money.
 */
class Amount implements AmountInterface {

  /**
   * The BC Math scale.
   */
  const BC_MATH_SCALE = 9;

  /**
   * The currency.
   *
   * @var \Commercie\Currency\CurrencyInterface
   */
  protected $currency;

  /**
   * The amount.
   *
   * @var int|string
   *   A numeric value.
   */
  protected $amount;

  /**
   * Creates a new instance.
   *
   * @param \Commercie\Currency\CurrencyInterface $currency
   * @param int|string $amount
   *   A numeric value.
   */
  public function __construct(CurrencyInterface $currency, $amount) {
    $this->amount = $amount;
    $this->currency = $currency;
  }

  public function getCurrency() {
    return $this->currency;
  }

  public function getAmount() {
    return $this->amount;
  }

  /**
   * Validates another amount against this one.
   *
   * @param \Commercie\Money\AmountInterface $amount
   *
   * @throws \InvalidArgumentException
   */
  protected function validateOtherAmount(AmountInterface $amount) {
    if ($this->getCurrency()->getCurrencyCode() !== $amount->getCurrency()->getCurrencyCode()) {
      throw new \InvalidArgumentException(sprintf('Both amounts must be in %s, but the other amount is in %s.', $this->getCurrency()->getCurrencyCode(), $amount->getCurrency()->getCurrencyCode()));
    }
  }

  public function add(AmountInterface $amount) {
    $this->validateOtherAmount($amount);

    return new static($this->getCurrency(), bcadd($this->getAmount(), $amount->getAmount(), static::BC_MATH_SCALE));
  }

  public function subtract(AmountInterface $amount) {
    $this->validateOtherAmount($amount);

    return new static($this->getCurrency(), bcsub($this->getAmount(), $amount->getAmount(), static::BC_MATH_SCALE));
  }

  public function multiplyBy($multiplier) {
    return new static($this->getCurrency(), bcmul($this->getAmount(), $multiplier, static::BC_MATH_SCALE));
  }

  public function divideBy($divisor) {
    return new static($this->getCurrency(), bcdiv($this->getAmount(), $divisor, static::BC_MATH_SCALE));
  }

  public function equals(AmountInterface $amount) {
    return $this->comparesTo($amount) === 0;
  }

  public function isMoreThan(AmountInterface $amount) {
    return $this->comparesTo($amount) === 1;
  }

  public function isLessThan(AmountInterface $amount) {
    return $this->comparesTo($amount) === -1;
  }

  /**
   * Compares this amount to another.
   *
   * @param \Commercie\Money\AmountInterface $amount
   *
   * @return int
   *   -1, 0, or 1 if this amount is respectively less than, equal to, or more
   *   than the other amount.
   */
  protected function comparesTo(AmountInterface $amount) {
    $this->validateOtherAmount($amount);

    return bccomp($this->getAmount(), $amount->getAmount(), static::BC_MATH_SCALE);
  }

}
