<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Contract_Amendment_Features extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Add Columns to 'contract_amendment_history' table ---
        // Add amendment_document_id
        if (!$this->db->field_exists('amendment_document_id', 'contract_amendment_history')) {
            $this->dbforge->add_column('contract_amendment_history', [
                'amendment_document_id' => [
                    'type' => 'BIGINT',
                    'null' => TRUE,
                ]
            ]);
        }

        // Add amendment_approval_status
        if (!$this->db->field_exists('amendment_approval_status', 'contract_amendment_history')) {
            $this->dbforge->add_column('contract_amendment_history', [
                'amendment_approval_status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ]
            ]);
        }

        // --- 2. Create 'contract_amendment_history_details' table ---
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'amendment_history_id' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'contract_id' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'field_name' => [
                'type' => 'NVARCHAR',
                'constraint' => '50',
                'null' => FALSE,
            ],
            'old_value' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX', // Use NVARCHAR(MAX) for SQL Server's TEXT equivalent
                'null' => TRUE,
            ],
            'new_value' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX', // Use NVARCHAR(MAX) for SQL Server's TEXT equivalent
                'null' => TRUE,
            ],
            'createdOn datetime DEFAULT GETDATE()', // Explicitly define default for CI
            'CONSTRAINT pk_contract_amendment_history_details PRIMARY KEY (id)',
            'CONSTRAINT FK_contract_amendment_history_details_amendment FOREIGN KEY (amendment_history_id) REFERENCES contract_amendment_history(id) ON DELETE CASCADE',
            'CONSTRAINT FK_contract_amendment_history_details_contract FOREIGN KEY (contract_id) REFERENCES contract(id)'
        ]);
        $this->dbforge->create_table('contract_amendment_history_details', TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop 'contract_amendment_history_details' table ---
        $this->dbforge->drop_table('contract_amendment_history_details', TRUE);

        // --- 2. Drop Columns from 'contract_amendment_history' table ---
        if ($this->db->field_exists('amendment_approval_status', 'contract_amendment_history')) {
            $this->dbforge->drop_column('contract_amendment_history', 'amendment_approval_status');
        }
        if ($this->db->field_exists('amendment_document_id', 'contract_amendment_history')) {
            $this->dbforge->drop_column('contract_amendment_history', 'amendment_document_id');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
