<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait RefreshDatabaseWithForce
{
    use RefreshDatabase {
        migrateFreshUsing as baseMigrateFreshUsing;
    }

    protected function migrateFreshUsing()
    {
        return array_merge($this->baseMigrateFreshUsing(), [
            '--force' => true,
        ]);
    }
}
