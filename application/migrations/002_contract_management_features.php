<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Contract_Management_Features extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Add Columns to Existing Tables ---

        // Add columns to 'Contract' table
        if (!$this->db->field_exists('category', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'category' => ['type' => 'NVARCHAR', 'constraint' => '50', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('stage', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'stage' => ['type' => 'NVARCHAR', 'constraint' => '50', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('milestone_visible_to_cp', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'milestone_visible_to_cp' => ['type' => 'TINYINT', 'null' => TRUE], // TINYINT typically 0-255
            ]);
        }
        // These columns were added in 'add_contract_columns', but including defensive checks here
        // in case this migration runs independently or needs to be idempotent.
        if (!$this->db->field_exists('contract_duration', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'contract_duration' => ['type' => 'INT', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('perf_security_commencement_date', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'perf_security_commencement_date' => ['type' => 'DATE', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('perf_security_expiry_date', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'perf_security_expiry_date' => ['type' => 'DATE', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('expected_completion_date', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'expected_completion_date' => ['type' => 'DATE', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('actual_completion_date', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'actual_completion_date' => ['type' => 'DATE', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('advance_payment_guarantee', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'advance_payment_guarantee' => ['type' => 'NVARCHAR', 'constraint' => '100', 'null' => TRUE], // Changed from TEXT to NVARCHAR(100) as per new schema
            ]);
        }
        if (!$this->db->field_exists('letter_of_credit_details', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'letter_of_credit_details' => [
                    'type' => 'NVARCHAR', // Use NVARCHAR(MAX) for TEXT equivalent
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
            ]);
        }
        if (!$this->db->field_exists('effective_date', 'Contract')) {
            $this->dbforge->add_column('Contract', [
                'effective_date' => ['type' => 'DATE', 'null' => TRUE],
            ]);
        }

        // Add 'applies_to' to 'contract_type_language'
        if (!$this->db->field_exists('applies_to', 'contract_type_language')) {
            $this->dbforge->add_column('contract_type_language', [
                'applies_to' => ['type' => 'NCHAR', 'constraint' => '10', 'null' => TRUE],
            ]);
        }

        // Add columns to 'contract_amendment_history'
        if (!$this->db->field_exists('amendment_document_id', 'contract_amendment_history')) {
            $this->dbforge->add_column('contract_amendment_history', [
                'amendment_document_id' => ['type' => 'BIGINT', 'null' => TRUE],
            ]);
        }
        if (!$this->db->field_exists('amendment_approval_status', 'contract_amendment_history')) {
            $this->dbforge->add_column('contract_amendment_history', [
                'amendment_approval_status' => ['type' => 'NVARCHAR', 'constraint' => '20', 'null' => TRUE],
            ]);
        }

        // --- 2. Create New Tables ---

        // Create 'contract_milestone' table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'contract_id' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => FALSE],
            'title' => ['type' => 'NVARCHAR', 'constraint' => '255', 'null' => FALSE],
            'serial_number' => ['type' => 'NVARCHAR', 'constraint' => '255', 'null' => TRUE, 'default' => NULL],
            'deliverables' => [
                'type' => 'NVARCHAR', // Use NVARCHAR(MAX) for TEXT equivalent
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'status' => ['type' => 'NVARCHAR', 'constraint' => '11', 'null' => TRUE, 'default' => 'open'],
            'financial_status' => ['type' => 'NVARCHAR', 'constraint' => '15', 'null' => TRUE, 'default' => NULL],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '32,12', 'null' => TRUE, 'default' => NULL],
            'currency_id' => ['type' => 'BIGINT', 'null' => TRUE, 'default' => NULL],
            'percentage' => ['type' => 'DECIMAL', 'constraint' => '22,10', 'null' => TRUE, 'default' => NULL],
            'start_date' => ['type' => 'DATE', 'null' => TRUE, 'default' => NULL],
            'due_date' => ['type' => 'DATE', 'null' => TRUE, 'default' => NULL],
            'createdOn datetime NULL', // SQL schema doesn't have default for createdOn here
            'createdBy' => ['type' => 'BIGINT', 'null' => TRUE],
            'modifiedOn datetime NULL',
            'modifiedBy' => ['type' => 'BIGINT', 'null' => TRUE],
            'channel' => ['type' => 'NCHAR', 'constraint' => '3', 'null' => TRUE],
            'CONSTRAINT pk_contract_milestone PRIMARY KEY (id)',
            'CONSTRAINT fk_contract_milestone_1 FOREIGN KEY (contract_id) REFERENCES contract(id)',
            'CONSTRAINT fk_contract_milestone_2 FOREIGN KEY (currency_id) REFERENCES iso_currencies(id)'
        ]);
        $this->dbforge->create_table('contract_milestone', TRUE);

        // Create 'contract_amendment_history_details' table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'amendment_history_id' => ['type' => 'BIGINT', 'null' => FALSE],
            'contract_id' => ['type' => 'BIGINT', 'null' => FALSE],
            'field_name' => ['type' => 'NVARCHAR', 'constraint' => '50', 'null' => FALSE],
            'old_value' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE
            ],
            'new_value' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE
            ],
            'createdOn datetime DEFAULT GETDATE()', // Explicit default
            'CONSTRAINT pk_contract_amendment_history_details PRIMARY KEY (id)',
            'CONSTRAINT FK_contract_amendment_history_details_amendment FOREIGN KEY (amendment_history_id) REFERENCES contract_amendment_history(id) ON DELETE CASCADE',
            'CONSTRAINT FK_contract_amendment_history_details_contract FOREIGN KEY (contract_id) REFERENCES contract(id)'
        ]);
        $this->dbforge->create_table('contract_amendment_history_details', TRUE);

        // Create 'contract_milestone_documents' table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'document_id' => ['type' => 'BIGINT', 'null' => FALSE],
            'milestone_id' => ['type' => 'BIGINT', 'null' => FALSE],
            'CONSTRAINT pk_contract_milestone_documents PRIMARY KEY (id)',
            'CONSTRAINT fk_contract_milestone_documents_1 FOREIGN KEY (document_id) REFERENCES documents_management_system(id) ON UPDATE CASCADE ON DELETE CASCADE',
            'CONSTRAINT fk_contract_milestone_documentse_2 FOREIGN KEY (milestone_id) REFERENCES contract_milestone(id) ON UPDATE CASCADE ON DELETE CASCADE'
        ]);
        $this->dbforge->create_table('contract_milestone_documents', TRUE);

        // --- 3. Insert System Preference Data ---
        $this->db->insert('system_preferences', [
            'groupName' => 'ContractDefaultValues',
            'keyName' => 'createNewContractOnAmendment',
            'keyValue' => 'no'
        ]);

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Remove System Preference Data ---
        $this->db->where('groupName', 'ContractDefaultValues');
        $this->db->where('keyName', 'createNewContractOnAmendment');
        $this->db->delete('system_preferences');

        // --- 2. Drop Tables (in reverse order of dependency) ---
        $this->dbforge->drop_table('contract_milestone_documents', TRUE);
        $this->dbforge->drop_table('contract_amendment_history_details', TRUE);
        $this->dbforge->drop_table('contract_milestone', TRUE);

        // --- 3. Drop Columns from Existing Tables ---

        // Drop columns from 'contract_amendment_history'
        if ($this->db->field_exists('amendment_approval_status', 'contract_amendment_history')) {
            $this->dbforge->drop_column('contract_amendment_history', 'amendment_approval_status');
        }
        if ($this->db->field_exists('amendment_document_id', 'contract_amendment_history')) {
            $this->dbforge->drop_column('contract_amendment_history', 'amendment_document_id');
        }

        // Drop 'applies_to' from 'contract_type_language'
        if ($this->db->field_exists('applies_to', 'contract_type_language')) {
            $this->dbforge->drop_column('contract_type_language', 'applies_to');
        }

        // Drop columns from 'Contract' table (in reverse order of addition or as desired)
        if ($this->db->field_exists('effective_date', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'effective_date');
        }
        if ($this->db->field_exists('letter_of_credit_details', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'letter_of_credit_details');
        }
        if ($this->db->field_exists('advance_payment_guarantee', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'advance_payment_guarantee');
        }
        if ($this->db->field_exists('actual_completion_date', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'actual_completion_date');
        }
        if ($this->db->field_exists('expected_completion_date', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'expected_completion_date');
        }
        if ($this->db->field_exists('perf_security_expiry_date', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'perf_security_expiry_date');
        }
        if ($this->db->field_exists('perf_security_commencement_date', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'perf_security_commencement_date');
        }
        if ($this->db->field_exists('contract_duration', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'contract_duration');
        }
        if ($this->db->field_exists('milestone_visible_to_cp', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'milestone_visible_to_cp');
        }
        if ($this->db->field_exists('stage', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'stage');
        }
        if ($this->db->field_exists('category', 'Contract')) {
            $this->dbforge->drop_column('Contract', 'category');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
