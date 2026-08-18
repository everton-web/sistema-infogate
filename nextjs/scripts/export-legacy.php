<?php
declare(strict_types=1);
$database = $argv[1] ?? dirname(__DIR__, 2).'/database/database.sqlite';
$output = $argv[2] ?? dirname(__DIR__).'/legacy-export.json';
if (!is_file($database)) { fwrite(STDERR, "Banco SQLite não encontrado: {$database}\n"); exit(1); }
$pdo = new PDO('sqlite:'.$database, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$tables = ['users','companies','branches','company_settings','company_user','branch_user','customers','vehicle_brands','vehicle_models','vehicles','products','suppliers','service_orders','service_order_items','stock_movements','sales','sale_items','quotes','quote_items','warranties','purchases','purchase_items','financial_entries','cash_registers','cash_register_transactions'];
$export = ['exported_at' => gmdate(DATE_ATOM), 'tables' => []];
foreach ($tables as $table) {
    $exists = $pdo->prepare("select 1 from sqlite_master where type='table' and name=?");
    $exists->execute([$table]);
    if ($exists->fetchColumn()) $export['tables'][$table] = $pdo->query('select * from "'.$table.'"')->fetchAll(PDO::FETCH_ASSOC);
}
file_put_contents($output, json_encode($export, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
fwrite(STDOUT, "Exportação criada em {$output}\n");
