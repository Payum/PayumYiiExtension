<?php

class add_payum_payment_tables extends CDbMigration
{
    public function up()
    {
        $this->createTable('payum_payment_token', array(
            'hash' => 'varchar(255) NOT NULL PRIMARY KEY',
            'payment_name' => 'string NOT NULL',
            'after_url' => 'text DEFAULT NULL',
            'target_url' => 'text NOT NULL',
            'details_id' => 'int(11) DEFAULT NULL',
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('payum_payment_details', array(
            'id' => 'pk',
            'number' => 'string NOT NULL',
            'description' => 'text DEFAULT NULL',
            'details' => 'blob DEFAULT NULL',
            'client_email' => 'string DEFAULT NULL',
            'client_id' => 'string DEFAULT NULL',
            'currency_code' => 'string DEFAULT NULL',
            'total_amount' => 'int(11) DEFAULT NULL',
            'currency_digits_after_decimal_point' => 'int(2) DEFAULT NULL',
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createIndex('IDX_payum_payment_token__details_id', 'payum_payment_token', 'details_id');
        $this->createIndex('IDX_payum_payment_details__number', 'payum_payment_details', 'number');
        $this->addForeignKey('FK_payment_token__payment_details', 'payum_payment_token', 'details_id', 'payum_payment_details', 'id', 'SET NULL');
    }

    public function down()
    {
        $this->dropForeignKey('FK_payment_token__payment_details', 'payum_payment_token');
        $this->dropTable('payum_payment_details');
        $this->dropTable('payum_payment_token');
    }
}
