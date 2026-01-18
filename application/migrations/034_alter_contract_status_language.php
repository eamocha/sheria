<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Contract_Status_Language extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Add the new columns to the 'contract_status_language' table
        $fields = [
            'responsible_user_roles' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'after' => 'status_id', // Adjust position as needed
            ],
            'step_icon' => [
                'type' => 'NVARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ],
            'activity' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'step_input' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'step_output' => [
                'type' => 'NVARCHAR',
                'constraint' => '250',
                'null' => TRUE,
            ],
        ];

        // Add columns only if they don't already exist
        foreach ($fields as $column_name => $field_definition) {
            if (!$this->db->field_exists($column_name, 'contract_status_language')) {
                $this->dbforge->add_column('contract_status_language', [$column_name => $field_definition]);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop the columns in the down migration
        if ($this->db->field_exists('responsible_user_roles', 'contract_status_language')) {
            $this->dbforge->drop_column('contract_status_language', 'responsible_user_roles');
        }
        if ($this->db->field_exists('step_icon', 'contract_status_language')) {
            $this->dbforge->drop_column('contract_status_language', 'step_icon');
        }
        if ($this->db->field_exists('activity', 'contract_status_language')) {
            $this->dbforge->drop_column('contract_status_language', 'activity');
        }
        if ($this->db->field_exists('step_input', 'contract_status_language')) {
            $this->dbforge->drop_column('contract_status_language', 'step_input');
        }
        if ($this->db->field_exists('step_output', 'contract_status_language')) {
            $this->dbforge->drop_column('contract_status_language', 'step_output');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
