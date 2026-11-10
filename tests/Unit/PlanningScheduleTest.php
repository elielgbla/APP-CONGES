<?php

namespace Tests\Unit;

use App\Models\Planning;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PlanningScheduleTest extends TestCase
{
    public function test_previous_schedule_ends_on_the_day_before_the_new_one(): void
    {
        $this->assertSame(
            '2026-11-09',
            Planning::getPreviousEndDate('2026-11-10', '2026-01-01')
        );
    }

    public function test_rejects_a_new_schedule_that_overlaps_its_start_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Planning::getPreviousEndDate('2026-11-10', '2026-11-10');
    }
}
