<?php
declare(strict_types=1);

use Carbon\Carbon;

class CarbonPeriod implements CarbonPeriodInterface
{
    /**
     * @var Carbon
     */
    protected $start;

    /**
     * @var Carbon
     */
    protected $finish;

    public function __construct(Carbon $start, Carbon $finish)
    {
        $this->start = $start;
        $this->finish = $finish;
    }

    final public function start(): Carbon
    {
        return $this->start;
    }

    final public function finish(): Carbon
    {
        return $this->finish;
    }

    public function overlap(CarbonPeriodInterface $another): bool
    {
        return $another->start() <= $this->finish && $another->finish() >= $this->start;
    }
}
