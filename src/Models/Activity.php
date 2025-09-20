<?php

namespace Nanorocks\FilamentActivityHistory\Models;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('activity-history.table_name', 'filament_activity_history');

        if ($connection = config('activity-history.database_connection')) {
            $this->setConnection($connection);
        }
    }
}
