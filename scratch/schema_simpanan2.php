<?php
$tables = ['penutupan_simpanan','pemindahbukuan_simpanan','deposito','simpanan_rencana','simpanan_rencana_detail','simpanan_kode','simpanan_jenis_kode','simpanan_bunga'];
foreach ($tables as $t) {
    echo "== $t ==" . PHP_EOL;
    $cols = \DB::select('select column_name, data_type from information_schema.columns where table_name = ? order by ordinal_position', [$t]);
    foreach ($cols as $c) {
        echo $c->column_name . ' : ' . $c->data_type . PHP_EOL;
    }
    echo PHP_EOL;
}
