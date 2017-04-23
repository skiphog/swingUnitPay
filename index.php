<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/app/UnitPayHandler.php';
require __DIR__ . '/SwingPay.php';

try {

    $swingPay = new SwingPay(
        new UnitPayHandler(),
        'vip_config'
    );

    $swingPay->run();

} catch (\UnitPayException $e) {
    http_response_code(403);
    die($e->getMessage());
}
