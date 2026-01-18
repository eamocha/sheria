<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Legal_Cases_Add_Next_Actions extends CI_Migration {

    private $table_name = 'legal_cases';
    private $column_name = 'next_actions';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Add 'next_actions' column (NVARCHAR(MAX) NULL) ---
        if (!$this->db->field_exists($this->column_name, $this->table_name)) {
            $this->dbforge->add_column($this->table_name, [
                $this->column_name => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX', // Using MAX for potentially long text
                    'null' => TRUE,
                ]
            ]);
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop 'next_actions' column ---
        if ($this->db->field_exists($this->column_name, $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, $this->column_name);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}