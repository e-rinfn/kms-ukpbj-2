<?php
if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tanggal, $cetak_hari = false)
    {
        $hari = array(1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        $split = explode('-', date('Y-m-d', strtotime($tanggal)));
        $tgl = $split[2];
        $bln = $split[1];
        $thn = $split[0];

        $jam = date('H:i', strtotime($tanggal));

        $tanggal_indo = $tgl . ' ' . $bulan[(int)$bln] . ' ' . $thn;

        if ($cetak_hari) {
            $num = date('N', strtotime($tanggal));
            return $hari[$num] . ', ' . $tanggal_indo;
        }
        return $tanggal_indo;
    }
}
