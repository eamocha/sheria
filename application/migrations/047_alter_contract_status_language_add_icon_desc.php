<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Contract_Status_Language_Add_Icon_Desc extends CI_Migration {

    private $table_name = 'contract_status_language';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Add 'step_icon' column ---
        if (!$this->db->field_exists('step_icon', $this->table_name)) {
            $this->dbforge->add_column($this->table_name, [
                'step_icon' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ]
            ]);
        }

        // --- Add 'description' column ---
        if (!$this->db->field_exists('description', $this->table_name)) {
            $this->dbforge->add_column($this->table_name, [
                'description' => [
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

        // --- Drop 'description' column ---
        if ($this->db->field_exists('description', $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, 'description');
        }

        // --- Drop 'step_icon' column ---
        if ($this->db->field_exists('step_icon', $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, 'step_icon');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}