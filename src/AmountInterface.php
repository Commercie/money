<?php

/**
 * @file
 * Contains \Commercie\Money\AmountInterface.
 */

namespace Commercie\Money;

/**
 * Defines an amount of money.
 */
interface AmountInterface {

  /**
   * Gets the currency.
   *
   * @return \Commercie\Currency\CurrencyInterface
   */
  public function getCurrency();

  /**
   * Gets the amount.
   *
   * @return string
   *   A numeric string.
   */
  public function getAmount();

  /**
   * Adds another amount to this one.
   *
   * @param self $amount
   *
   * @return static
   *
   * @throws \InvalidArgumentException
   *   Thrown when the other amount's currency does not match.
   */
  public function add(self $amount);

  /**
   * Subtracts another amount from this one.
   *
   * @param self $amount
   *
   * @return static
   *
   * @throws \InvalidArgumentException
   *   Thrown when the other amount's currency does not match.
   */
  public function subtract(self $amount);

  /**
   * Multiplies the amount.
   *
   * @param int|string $multiplier
   *   A numeric value.
   *
   * @return static
   */
  public function multiplyBy($multiplier);

  /**
   * Divides the amount.
   *
   * @param int|string $divisor
   *   A numeric value.
   *
   * @return static
   */
  public function divideBy($divisor);

  /**
   * Checks if the amount equals another.
   *
   * @param self $amount
   *
   * @return bool
   *
   * @throws \InvalidArgumentException
   *   Thrown when the other amount's currency does not match.
   */
  public function equals(self $amount);

  /**
   * Checks if the amount is more than another.
   *
   * @param self $amount
   *
   * @return bool
   *
   * @throws \InvalidArgumentException
   *   Thrown when the other amount's currency does not match.
   */
  public function isMoreThan(self $amount);

  /**
   * Checks if the amount is less than another.
   *
   * @param self $amount
   *
   * @return bool
   *
   * @throws \InvalidArgumentException
   *   Thrown when the other amount's currency does not match.
   */
  public function isLessThan(self $amount);

}
