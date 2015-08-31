<?php

/**
 * @file Contains \Commercie\Tests\Money\AmountTest.
 */

namespace Commercie\Tests\Money;

use Commercie\Currency\CurrencyInterface;
use Commercie\Money\Amount;
use Commercie\Money\AmountInterface;

/**
 * @coversDefaultClass \Commercie\Money\Amount
 */
class AmountTest extends \PHPUnit_Framework_TestCase
{

  /**
   * The currency.
   *
   * @var \Commercie\Currency\CurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
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
   * The subject under test.
   *
   * @var \Commercie\Money\Amount
   */
  protected $sut;

  public function setUp()
  {
    parent::setUp();

    $this->currency = $this->getMock(CurrencyInterface::class);

    $this->amount = mt_rand();

    $this->sut = new Amount($this->currency, $this->amount);
  }

  /**
   * @covers ::getCurrency
   * @covers ::__construct
   */
  public function testGetCurrency()
  {
    $this->assertSame($this->currency, $this->sut->getCurrency());
  }

  /**
   * @covers ::getAmount
   * @covers ::__construct
   */
  public function testGetAmount()
  {
    $this->assertSame($this->amount, $this->sut->getAmount());
  }

  /**
   * @covers ::add
   * @covers ::validateOtherAmount
   *
   * @dataProvider providerAdd
   */
  public function testAdd($expectedAmount, $sutAmountAmount, $otherAmountAmount)
  {
    $currencyCode = str_shuffle('ABC');

    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getAmount')
      ->willReturn($otherAmountAmount);

    $newAmount = $this->sut->add($otherAmount);
    $this->assertInstanceOf(AmountInterface::class, $newAmount);
    $this->assertSame($expectedAmount, $newAmount->getAmount());
  }

  /**
   * Provides data to self::testAdd().
   */
  public function providerAdd() {
    return [
      ['6.530000000', 4, '2.53'],
      ['6.530000000', '4.03', '2.5'],
    ];
  }

  /**
   * @covers ::add
   * @covers ::validateOtherAmount
   *
   * @expectedException \InvalidArgumentException
   */
  public function testAddWithInvalidCurrencies()
  {
    $sutCurrencyCode = str_shuffle('FOO');
    $otherCurrencyCode = str_shuffle('BAR');

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($sutCurrencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($otherCurrencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);

    $this->sut->add($otherAmount);
  }

  /**
   * @covers ::subtract
   * @covers ::validateOtherAmount
   *
   * @dataProvider providerSubtract
   */
  public function testSubtract($expectedAmount, $sutAmountAmount, $otherAmountAmount)
  {
    $currencyCode = str_shuffle('ABC');

    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getAmount')
      ->willReturn($otherAmountAmount);

    $newAmount = $this->sut->subtract($otherAmount);
    $this->assertInstanceOf(AmountInterface::class, $newAmount);
    $this->assertSame($expectedAmount, $newAmount->getAmount());
  }

  /**
   * Provides data to self::testSubtract().
   */
  public function providerSubtract() {
    return [
      ['1.470000000', 4, '2.53'],
      ['1.530000000', '4.03', '2.5'],
    ];
  }

  /**
   * @covers ::subtract
   * @covers ::validateOtherAmount
   *
   * @expectedException \InvalidArgumentException
   */
  public function testSubtractWithInvalidCurrencies()
  {
    $sutCurrencyCode = str_shuffle('FOO');
    $otherCurrencyCode = str_shuffle('BAR');

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($sutCurrencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($otherCurrencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);

    $this->sut->subtract($otherAmount);
  }

  /**
   * @covers ::multiplyBy
   * @covers ::validateNumber
   *
   * @dataProvider providerMultiplyBy
   */
  public function testMultiplyBy($expectedAmount, $sutAmountAmount, $multiplier)
  {
    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $newAmount = $this->sut->multiplyBy($multiplier);
    $this->assertInstanceOf(AmountInterface::class, $newAmount);
    $this->assertSame($expectedAmount, $newAmount->getAmount());
  }

  /**
   * Provides data to self::testMultiplyBy().
   */
  public function providerMultiplyBy() {
    return [
      // @todo It seems the output seems to be missing some extra decimals.
      //   Research whether that is indeed to be expected from BC Math, or
      //   whether this is a bug.
      ['5.06', '2.53', 2],
      ['5.06', '2.53', '2'],
      ['6.325', '2.53', '2.5'],
    ];
  }

  /**
   * @covers ::multiplyBy
   * @covers ::validateNumber
   *
   * @expectedException \InvalidArgumentException
   *
   * @dataProvider providerMultiplyByWithInvalidMultiplier
   */
  public function testMultiplyByWithInvalidMultiplier($multiplier)
  {
    $this->sut->multiplyBy($multiplier);
  }

  /**
   * Provides data to self::testMultiplyByWithInvalidMultiplier().
   */
  public function providerMultiplyByWithInvalidMultiplier() {
    return [
      ['foo'],
      [TRUE],
      [new \stdClass()],
    ];
  }

  /**
   * @covers ::divideBy
   * @covers ::validateNumber
   *
   * @dataProvider providerDivideBy
   */
  public function testDivideBy($expectedAmount, $sutAmountAmount, $divisor)
  {
    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $newAmount = $this->sut->divideBy($divisor);
    $this->assertInstanceOf(AmountInterface::class, $newAmount);
    $this->assertSame($expectedAmount, $newAmount->getAmount());
  }

  /**
   * Provides data to self::testDivideBy().
   */
  public function providerDivideBy() {
    return [
      ['1.265000000', '2.53', 2],
      ['1.265000000', '2.53', '2'],
      ['1.000000000', '2.53', '2.53'],
    ];
  }

  /**
   * @covers ::divideBy
   * @covers ::validateNumber
   *
   * @expectedException \InvalidArgumentException
   *
   * @dataProvider providerDivideByWithInvalidDivisor
   */
  public function testDivideByWithInvalidDivisor($divisor)
  {
    $this->sut->divideBy($divisor);
  }

  /**
   * Provides data to self::testDivideByWithInvalidDivisor().
   */
  public function providerDivideByWithInvalidDivisor() {
    return [
      [0],
      ['foo'],
      [TRUE],
      [new \stdClass()],
    ];
  }

  /**
   * @covers ::equals
   * @covers ::comparesTo
   * @covers ::validateOtherAmount
   *
   * @dataProvider providerEquals
   */
  public function testEquals($expected, $sutAmountAmount, $otherAmountAmount)
  {
    $currencyCode = str_shuffle('ABC');

    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getAmount')
      ->willReturn($otherAmountAmount);

    $this->assertSame($expected, $this->sut->equals($otherAmount));
  }

  /**
   * Provides data to self::testEquals().
   */
  public function providerEquals() {
    return [
      [true, 2, 2],
      [true, 2, '2'],
      [true, '2.53', '2.53'],
      [false, 2, '2.53'],
      [false, 2, 1],
      [false, '2.53', '2.54'],
    ];
  }

  /**
   * @covers ::equals
   * @covers ::comparesTo
   * @covers ::validateOtherAmount
   *
   * @expectedException \InvalidArgumentException
   */
  public function testEqualsWithInvalidCurrencies()
  {
    $sutCurrencyCode = str_shuffle('FOO');
    $otherCurrencyCode = str_shuffle('BAR');

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($sutCurrencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($otherCurrencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);

    $this->sut->equals($otherAmount);
  }

  /**
   * @covers ::isMoreThan
   * @covers ::comparesTo
   * @covers ::validateOtherAmount
   *
   * @dataProvider providerIsMoreTHan
   */
  public function testIsMoreThan($expected, $sutAmountAmount, $otherAmountAmount)
  {
    $currencyCode = str_shuffle('ABC');

    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getAmount')
      ->willReturn($otherAmountAmount);

    $this->assertSame($expected, $this->sut->isMoreThan($otherAmount));
  }

  /**
   * Provides data to self::testIsMoreThan().
   */
  public function providerIsMoreTHan() {
    return [
      [true, '2.53', 2],
      [true, 2, 1],
      [true, '2.54', '2.53'],
      [false, 2, 2],
      [false, 2, '2'],
      [false, '2.53', '2.53'],
    ];
  }

  /**
   * @covers ::isMoreThan
   * @covers ::comparesTo
   * @covers ::validateOtherAmount
   *
   * @expectedException \InvalidArgumentException
   */
  public function testIsMoreThanWithInvalidCurrencies()
  {
    $sutCurrencyCode = str_shuffle('FOO');
    $otherCurrencyCode = str_shuffle('BAR');

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($sutCurrencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($otherCurrencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);

    $this->sut->isMoreThan($otherAmount);
  }

  /**
   * @covers ::isLessThan
   * @covers ::comparesTo
   * @covers ::validateOtherAmount
   *
   * @dataProvider providerIsLessThan
   */
  public function testIsLessThan($expected, $sutAmountAmount, $otherAmountAmount)
  {
    $currencyCode = str_shuffle('ABC');

    $this->sut = new Amount($this->currency, $sutAmountAmount);

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($currencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getAmount')
      ->willReturn($otherAmountAmount);

    $this->assertSame($expected, $this->sut->isLessThan($otherAmount));
  }

  /**
   * Provides data to self::testIsLessThan().
   */
  public function providerIsLessThan() {
    return [
      [true, 2, '2.53'],
      [true, 1, 2],
      [true, '2.53', '2.54'],
      [false, 2, 2],
      [false, 2, '2'],
      [false, '2.53', '2.53'],
    ];
  }

  /**
   * @covers ::isLessThan
   * @covers ::comparesTo
   * @covers ::validateOtherAmount
   *
   * @expectedException \InvalidArgumentException
   */
  public function testIsLessThanWithInvalidCurrencies()
  {
    $sutCurrencyCode = str_shuffle('FOO');
    $otherCurrencyCode = str_shuffle('BAR');

    $this->currency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($sutCurrencyCode);

    $otherCurrency = $this->getMock(CurrencyInterface::class);
    $otherCurrency->expects($this->atLeastOnce())
      ->method('getCurrencyCode')
      ->willReturn($otherCurrencyCode);

    $otherAmount = $this->getMock(AmountInterface::class);
    $otherAmount->expects($this->atLeastOnce())
      ->method('getCurrency')
      ->willReturn($otherCurrency);

    $this->sut->isLessThan($otherAmount);
  }

}
