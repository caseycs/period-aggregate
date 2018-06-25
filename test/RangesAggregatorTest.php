<?php
declare(strict_types=1);

class RangesAggregatorTest extends PHPUnit\Framework\TestCase
{
    public function testAddWithoutCross()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(180, 240);
        $this->assertSame([[60, 120], [180, 240]], $aggregator->ranges());
    }

    public function testSame()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(60, 120);
        $this->assertSame([[60, 120]], $aggregator->ranges());
    }

    public function testAddTouchStart()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(30, 60);
        $this->assertSame([[30, 120]], $aggregator->ranges());
    }

    public function testAddTouchFinish()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(120, 180);
        $this->assertSame([[60, 180]], $aggregator->ranges());
    }

    public function testAddOverlapStart()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(30, 100);
        $this->assertSame([[30, 120]], $aggregator->ranges());
    }

    public function testAddOverlapFinish()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 110);
        $aggregator->add(100, 180);
        $this->assertSame([[60, 180]], $aggregator->ranges());
    }

    public function testAddOverlapComplete()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(30, 180);
        $this->assertSame([[30, 180]], $aggregator->ranges());
    }

    public function testAddOverlapInTheMiddle()
    {
        $aggregator = new \RangesAggregator();
        $aggregator->add(60, 120);
        $aggregator->add(80, 100);
        $this->assertSame([[60, 120]], $aggregator->ranges());
    }
}
