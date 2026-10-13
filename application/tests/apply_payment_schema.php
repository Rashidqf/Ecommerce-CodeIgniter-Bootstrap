<?php
$mysqli = new mysqli('localhost', 'root', '', 'shop');
if ($mysqli->connect_error) {
    fwrite(STDERR, $mysqli->connect_error . PHP_EOL);
    exit(1);
}
$sql = file_get_contents(dirname(__DIR__) . '/../database/migrations/20261013_buyora_payments.sql');
if (!$mysqli->multi_query($sql)) {
    fwrite(STDERR, $mysqli->error . PHP_EOL);
    exit(1);
}
do {
    if ($result = $mysqli->store_result()) {
        $result->free();
    }
} while ($mysqli->more_results() && $mysqli->next_result());
if ($mysqli->errno) {
    fwrite(STDERR, $mysqli->error . PHP_EOL);
    exit(1);
}
$column = $mysqli->query("SHOW COLUMNS FROM vendors_orders LIKE 'parent_order_id'");
if ($column && $column->num_rows === 0) {
    if (!$mysqli->query('ALTER TABLE vendors_orders ADD COLUMN parent_order_id int unsigned NULL DEFAULT NULL')) {
        fwrite(STDERR, $mysqli->error . PHP_EOL);
        exit(1);
    }
    echo "added parent_order_id\n";
} else {
    echo "parent_order_id already present\n";
}

$mysqli->begin_transaction();
$mysqli->query("INSERT INTO vendor_payouts (vendor_id, amount_minor, method, reference_code, notes, paid_on, status, admin_user, created_at) VALUES (999999, 100, 'bank', 'TEST-REF', '', '2026-10-13', 'recorded', 'test', 1)");
$first = $mysqli->affected_rows === 1;
$duplicateBlocked = false;
try {
    $mysqli->query("INSERT INTO vendor_payouts (vendor_id, amount_minor, method, reference_code, notes, paid_on, status, admin_user, created_at) VALUES (999999, 100, 'bank', 'TEST-REF', '', '2026-10-13', 'recorded', 'test', 1)");
} catch (mysqli_sql_exception $exception) {
    $duplicateBlocked = $exception->getCode() === 1062;
}
$mysqli->rollback();
if (!$first || !$duplicateBlocked) {
    fwrite(STDERR, "duplicate payout test failed\n");
    exit(1);
}
$left = $mysqli->query("SELECT COUNT(*) AS num FROM vendor_payouts WHERE vendor_id = 999999")->fetch_assoc();
if ((int) $left['num'] !== 0) {
    fwrite(STDERR, "test payout was not rolled back\n");
    exit(1);
}
echo "migration ok\n";
echo "duplicate payout reference blocked\n";
