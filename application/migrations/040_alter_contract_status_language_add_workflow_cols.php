<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Contract_Status_Language_Add_Workflow_Cols extends CI_Migration {

    private $table = 'contract_status_language';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        $fields = [
            'responsible_user_roles' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'comment' => 'User roles authorized for this workflow step.'
            ],
            'step_icon' => [
                'type' => 'NVARCHAR',
                'constraint' => '50',
                'null' => TRUE,
                'comment' => 'Icon class for displaying the workflow step.'
            ],
            'activity' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
                'comment' => 'A detailed description of the activity/action taken at this step.'
            ],
            'step_input' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'comment' => 'Defines the expected input format for this step.'
            ],
            'step_output' => [
                'type' => 'NVARCHAR',
                'constraint' => '250',
                'null' => TRUE,
                'comment' => 'Defines the expected output format or result of this step.'
            ]
        ];

        foreach ($fields as $column => $definition) {
            if (!$this->db->field_exists($column, $this->table)) {
                $this->dbforge->add_column($this->table, [$column => $definition]);
            }
        }
        
        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        $columns_to_drop = [
            'responsible_user_roles',
            'step_icon',
            'activity',
            'step_input',
            'step_output'
        ];

        foreach ($columns_to_drop as $column) {
            if ($this->db->field_exists($column, $this->table)) {
                $this->dbforge->drop_column($this->table, $column);
            }
        }
        
        $this->db->trans_complete(); // Complete the transaction
    }
}
