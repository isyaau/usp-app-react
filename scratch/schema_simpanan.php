<?php
$tables = ['simpanan','simpanan_jenis','setoran_simpanan','tarikan_simpanan'];
foreach ($tables as $t) {
    echo "== $t ==" . PHP_EOL;
    $cols = \DB::select('select column_name, data_type from information_schema.columns where table_name = ? order by ordinal_position', [$t]);
    foreach ($cols as $c) {
        echo $c->column_name . ' : ' . $c->data_type . PHP_EOL;
    }
    echo PHP_EOL;
}
