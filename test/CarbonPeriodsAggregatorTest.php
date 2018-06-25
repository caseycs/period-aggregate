<?php
declare(strict_types=1);

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CarbonPeriodsAggregatorTest extends TestCase
{
    /**
     * @var Carbon
     */
    private $now;

    public function setUp()
    {
        $this->now = Carbon::now()->startOfDay();
    }

    public function testAddWithoutCross()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(1, 2));
        $aggregator->add($this->period(3, 4));
        $this->assertRanges(
            [
                $this->period(1, 2),
                $this->period(3, 4),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testSame()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(1, 2));
        $aggregator->add($this->period(1, 2));
        $this->assertRanges(
            [
                $this->period(1, 2),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testAddTouchStart()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(1, 2));
        $aggregator->add($this->period(0, 1));
        $this->assertRanges(
            [
                $this->period(0, 2),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testAddTouchFinish()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(1, 2));
        $aggregator->add($this->period(2, 3));
        $this->assertRanges(
            [
                $this->period(1, 3),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testAddOverlapStart()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(5, 10));
        $aggregator->add($this->period(3, 7));
        $this->assertRanges(
            [
                $this->period(3, 10),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testAddOverlapFinish()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(5, 10));
        $aggregator->add($this->period(7, 15));
        $this->assertRanges(
            [
                $this->period(5, 15),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testAddOverlapComplete()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(5, 10));
        $aggregator->add($this->period(3, 15));
        $this->assertRanges(
            [
                $this->period(3, 15),
            ],
            $aggregator->rangesSorted()
        );
    }

    public function testAddOverlapInTheMiddle()
    {
        $aggregator = new CarbonPeriodsAggregator();
        $aggregator->add($this->period(5, 10));
        $aggregator->add($this->period(7, 9));
        $this->assertRanges(
            [
                $this->period(5, 10),
            ],
            $aggregator->rangesSorted()
        );
    }

    /**
     * @param array|CarbonPeriodInterface[] $expected
     * @param array|CarbonPeriodInterface[] $rangesSorted
     */
    private function assertRanges(array $rangesExpected, array $rangesSorted): void
    {
        $this->assertCount(count($rangesExpected), $rangesSorted);
        foreach ($rangesSorted as $index => $period) {
            $expected = $rangesExpected[$index];

            $this->assertSame(
                $expected->start()->format('Y-m-d H:i:s') . ' - ' . $expected->finish()->format('Y-m-d H:i:s'),
                $period->start()->format('Y-m-d H:i:s') . ' - ' . $period->finish()->format('Y-m-d H:i:s')
            );
        }
    }

    private function period(int $addHoursStart, int $addHoursFinish): CarbonPeriod
    {
        return new CarbonPeriod(
            $this->now->copy()->addHours($addHoursStart),
            $this->now->copy()->addHours($addHoursFinish)
        );
    }
}
