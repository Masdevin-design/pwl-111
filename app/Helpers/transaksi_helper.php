<?php

if (! function_exists('hitung_ppn')) {
    function hitung_ppn($total_harga)
    {
        return $total_harga * 0.11;
    }
}

if (! function_exists('hitung_biaya_admin')) {
    function hitung_biaya_admin($total_harga)
    {
        if ($total_harga <= 20000000) {
            $rate = 0.006;
        } elseif ($total_harga <= 40000000) {
            $rate = 0.008;
        } else {
            $rate = 0.010;
        }

        return $total_harga * $rate;
    }
}

if (! function_exists('hitung_diskon_voucher')) {
    function hitung_diskon_voucher($total_harga, $voucher_code)
    {
        $vouchers = [
            'FLASH10'  => 0.10,
            'FLASH15'  => 0.15,
            'MEMBER20' => 0.20,
        ];

        $code = strtoupper(trim((string) $voucher_code));

        if (! isset($vouchers[$code])) {
            return 0;
        }

        return $total_harga * $vouchers[$code];
    }
}