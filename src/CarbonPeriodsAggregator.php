<?php
declare(strict_types=1);

class CarbonPeriodsAggregator
{
    /**
     * @var CarbonPeriodInterface[]
     */
    protected $periods = [];

    public function add(CarbonPeriodInterface $new): void
    {
        foreach ($this->periods as $index => $existing) {
            if (!$new->overlap($existing)) {
                // no overlap
                continue;
            }

            if ($new->start() <= $existing->start() && $new->finish() >= $existing->finish()) {
                // full overlap or equal - overwrite
                $this->periods[$index] = $new;
                return;
            } elseif ($new->start() >= $existing->start() && $new->finish() <= $existing->finish()) {
                // in the middle - do nothing
                return;
            } elseif ($new->start() <= $existing->start() && $new->finish() <= $existing->finish()) {
                // start overlap - decrease start
                $this->periods[$index] = new CarbonPeriod($new->start(), $existing->finish());
                return;
            } elseif ($new->start() >= $existing->start() && $new->finish() >= $existing->finish()) {
                // finish overlap - increase finish
                $this->periods[$index] = new CarbonPeriod($existing->start(), $new->finish());
                return;
            } else {
                throw new \LogicException;
            }
        }

        // no overlap found
        $this->periods[] = $new;
    }

    /**
     * @return CarbonPeriodInterface[]
     */
    public function ranges(): array
    {
        return $this->periods;
    }

    /**
     * @return CarbonPeriodInterface[]
     */
    public function rangesSorted(): array
    {
        usort(
            $this->periods,
            function (CarbonPeriodInterface $a, CarbonPeriodInterface $b) {
                return $a->start() <=> $b->start();
            }
        );
        return $this->periods;
    }
}
