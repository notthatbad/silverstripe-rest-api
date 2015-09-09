<?php

SS_Cache::add_backend(
    'session_cache',
    'Memcached',
    array(
        'servers' => array(
            'host' => 'localhost',
            'port' => 11211,
            'persistent' => true,
            'weight' => 1,
            'timeout' => 5,
            'retry_interval' => 15,
            'status' => true
        )
    )
);
SS_Cache::pick_backend('session_cache', 'any', 10);