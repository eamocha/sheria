<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Otp_Columns_To_Users extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Add 'otp_code' column
        if (!$this->db->field_exists('otp_code', 'users')) {
            $this->dbforge->add_column('users', [
                'otp_code' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '10',
                    'null' => TRUE,
                ]
            ]);
        }

        // Add 'otp_expiry' column
        if (!$this->db->field_exists('otp_expiry', 'users')) {
            $this->dbforge->add_column('users', [
                'otp_expiry' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ]
            ]);
        }

        // Add 'last_otp_verified_at' column
        if (!$this->db->field_exists('last_otp_verified_at', 'users')) {
            $this->dbforge->add_column('users', [
                'last_otp_verified_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ]
            ]);
        }

        // Add 'last_login_device_fingerprint' column
        if (!$this->db->field_exists('last_login_device_fingerprint', 'users')) {
            $this->dbforge->add_column('users', [
                'last_login_device_fingerprint' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '255',
                    'null' => TRUE,
                ]
            ]);
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop columns in reverse order of addition
        if ($this->db->field_exists('last_login_device_fingerprint', 'users')) {
            $this->dbforge->drop_column('users', 'last_login_device_fingerprint');
        }
        if ($this->db->field_exists('last_otp_verified_at', 'users')) {
            $this->dbforge->drop_column('users', 'last_otp_verified_at');
        }
        if ($this->db->field_exists('otp_expiry', 'users')) {
            $this->dbforge->drop_column('users', 'otp_expiry');
        }
        if ($this->db->field_exists('otp_code', 'users')) {
            $this->dbforge->drop_column('users', 'otp_code');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
