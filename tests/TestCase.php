<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Assert that a record was removed, tolerating hard deletes when the
     * model does not (yet) use the SoftDeletes trait.
     */
    protected function assertModelRemoved(string $modelClass, Model $model): void
    {
        if (in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            $this->assertSoftDeleted($model);

            return;
        }

        $this->assertDatabaseMissing((new $modelClass)->getTable(), [
            'id' => $model->getKey(),
        ]);
    }
}
