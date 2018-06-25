<?php
declare(strict_types=1);

class RangesAggregator
{
    /**
     * @var array[]
     */
    protected $ranges = [];

    public function add(int $start, int $finish): void
    {
        foreach ($this->ranges as $index => list($startExisting, $finishExisting)) {
            if (!($start <= $finishExisting && $finish >= $startExisting)) {
                // no overlap
                continue;
            }

            if ($start <= $startExisting && $finish >= $finishExisting) {
                // full overlap or equal - overwrite
                $this->ranges[$index] = [$start, $finish];
                return;
            } elseif ($start >= $startExisting && $finish <= $finishExisting) {
                // in the middle - do nothing
                return;
            } elseif ($start <= $startExisting && $finish <= $finishExisting) {
                // start overlap - decrease start
                $this->ranges[$index][0] = $start;
                return;
            } elseif ($start >= $startExisting && $finish >= $finishExisting) {
                // finish overlap - increase finish
                $this->ranges[$index][1] = $finish;
                return;
            } else {
                throw new \LogicException;
            }
        }

        // no overlap found
        $this->ranges[] = [$start, $finish];
    }

    public function ranges(): array
    {
        return $this->ranges;
    }

    public function rangesSorted(): array
    {
        usort(
            $this->ranges,
            function (array $a, array $b) {
                return $a[0] <=> $b[0];
            }
        );
        return $this->ranges;
    }
}
