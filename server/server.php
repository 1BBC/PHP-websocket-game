<?php

use Workerman\Worker;

require_once __DIR__ . '/../vendor/autoload.php';

$connections = [];
$testCon = 0;

$ws_worker = new Worker('websocket://0.0.0.0:7777');

// 1 processes
$ws_worker->count = 1;

// Emitted when new connection come
$ws_worker->onConnect = function ($connection) use(&$connections) {
    $connection->onWebSocketConnect = function($connection) use (&$connections)
    {
        $connection->room_id = empty($_GET['room_id']) ? 1 : $_GET['room_id'];
        $connections[$connection->room_id][$connection->id] = $connection;
    };

    echo "New connection #" . $connection->id . "\n";
};

$ws_worker->onMessage = function($connection, $message) use (&$connections)
{
    $messageData = json_decode($message, true);

    if (isset($connections[$connection->room_id])) {
        foreach ($connections[$connection->room_id] as $k => $v) {
            $v->send(json_encode($messageData));
        }
    }
};

// Emitted when connection closed
$ws_worker->onClose = function ($connection) use (&$connections) {
    if (!isset($connections[$connection->room_id][$connection->id])) {
        return;
    }

    if (count($connections[$connection->room_id]) <=1 ) {
        unset($connections[$connection->room_id]);
    } else {
        unset($connections[$connection->room_id][$connection->id]);
    }

    echo "Connection closed: #" . $connection->id . "\n";
};

// Run worker
Worker::runAll();