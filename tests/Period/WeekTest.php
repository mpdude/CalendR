<?php

namespace CalendR\Test\Period;

use CalendR\Period\Factory;
use CalendR\Period\FactoryInterface;
use CalendR\Period\Week;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WeekTest extends TestCase
{
    use ProphecyTrait;

    public static function providerConstructValid()
    {
        return array(
            array(new \DateTime('2012-01-02')),
            array(new \DateTime('2012-01-09')),
            array(new \DateTime('2012-01-23')),
        );
    }

    public static function providerContains()
    {
        return array(
            array(new \DateTime('2012-01-02'), new \DateTime('2012-01-04'), new \DateTime('2012-01-09')),
            array(new \DateTime('2012-01-09'), new \DateTime('2012-01-09'), new \DateTime('2012-01-19')),
            array(new \DateTime('2012-01-09'), new \DateTime('2012-01-09'), new \DateTime('2011-01-10')),
            array(new \DateTime('2012-01-09'), new \DateTime('2012-01-09'), new \DateTime('2012-01-17')),
        );
    }

    public static function providerNumber()
    {
        return array(
            array(new \DateTime('2012-01-02'), 1),
            array(new \DateTime('2012-01-09'), 2),
            array(new \DateTime('2011-12-26'), 52),
        );
    }

    /**
     * @dataProvider providerContains
     */
    public function testContains($start, $contain, $notContain)
    {
        $week = new Week($start, $this->prophesize(FactoryInterface::class)->reveal());

        $this->assertTrue($week->contains($contain));
        $this->assertFalse($week->contains($notContain));
    }

    /**
     * @dataProvider providerNumber
     */
    public function testNumber($start, $number)
    {
        $week = new Week($start, $this->prophesize(FactoryInterface::class)->reveal());

        $this->assertEquals($week->getNumber(), $number);
    }

    /**
     * @dataProvider providerConstructValid
     */
    public function testConstructValid($start)
    {
        $week = new Week($start, $this->prophesize(FactoryInterface::class)->reveal());

        $this->assertInstanceOf(Week::class, $week);
    }

    public function testIteration()
    {
        $start = new \DateTime('2012-W01');
        $week = new Week($start, new Factory);

        $i = 0;

        foreach ($week as $dayKey => $day) {
            $this->assertGreaterThan(0, preg_match('/^\\d{2}\\-\\d{2}\\-\\d{4}$/', $dayKey));
            $this->assertSame($start->format('d-m-Y'), $day->getBegin()->format('d-m-Y'));
            $start->add(new \DateInterval('P1D'));
            $i++;
        }

        $this->assertEquals($i, 7);
    }

    public function testGetDatePeriod()
    {
        $date = new \DateTime('2012-01-01');
        $week = new Week($date, $this->prophesize(FactoryInterface::class)->reveal());
        foreach ($week->getDatePeriod() as $dateTime) {
            $this->assertEquals($date->format('Y-m-d'), $dateTime->format('Y-m-d'));
            $date->add(new \DateInterval('P1D'));
        }
    }

    public function testToString()
    {
        $date = new \DateTime(date('Y-\\WW'));
        $week = new Week($date, $this->prophesize(FactoryInterface::class)->reveal());
        $this->assertSame($date->format('W'), (string) $week);
    }
}
