<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaxFieldsToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'ppn' => [
                'type' => 'DOUBLE',
                'null' => true,
                'after' => 'ongkir',
            ],
            'biaya_admin' => [
                'type' => 'DOUBLE',
                'null' => true,
                'after' => 'ppn',
            ],
            'voucher_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'biaya_admin',
            ],
            'diskon_voucher' => [
                'type' => 'DOUBLE',
                'null' => true,
                'after' => 'voucher_code',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', ['ppn', 'biaya_admin', 'voucher_code', 'diskon_voucher']);
    }
}