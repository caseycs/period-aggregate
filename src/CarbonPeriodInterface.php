<?php
declare(strict_types=1);

use Carbon\Carbon;

interface CarbonPeriodInterface
{
    public function start(): Carbon;

    public function finish(): Carbon;

    public function overlap(CarbonPeriodInterface $another): bool;
}
