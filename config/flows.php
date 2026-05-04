<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hot Retention Window
    |--------------------------------------------------------------------------
    | Number of months of flow_sessions data to keep in the partitioned table.
    | The RotateFlowPartitions command drops partitions older than this.
    | Set to 1 initially; raise to 3 once storage budget is confirmed.
    */
    'retention_months' => (int) env('FLOW_RETENTION_MONTHS', 1),

    /*
    |--------------------------------------------------------------------------
    | Queue Name
    |--------------------------------------------------------------------------
    | The queue on which ProcessFlowBatch jobs run.
    | Run a dedicated worker: php artisan queue:work --queue=flow-ingest
    */
    'queue' => env('FLOW_QUEUE', 'flow-ingest'),

    /*
    |--------------------------------------------------------------------------
    | Bulk Insert Chunk Size
    |--------------------------------------------------------------------------
    | Max rows per DB::insert() call inside ProcessFlowBatch.
    | 500 is a safe default; raise if average batch size is very large and
    | DB round-trip latency is the bottleneck.
    */
    'insert_chunk_size' => (int) env('FLOW_INSERT_CHUNK_SIZE', 500),

];
