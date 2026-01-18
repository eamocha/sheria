<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Legal_Cases_Approval_Step extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Check if the column exists and if it has a default constraint
        if ($this->db->field_exists('approval_step', 'legal_cases')) {
            // Find and drop any default constraints on 'approval_step'
            $query_default_constraint = $this->db->query("
                SELECT dc.name
                FROM sys.default_constraints dc
                INNER JOIN sys.columns c ON dc.parent_object_id = c.object_id AND dc.parent_column_id = c.column_id
                WHERE dc.parent_object_id = OBJECT_ID('legal_cases') AND c.name = 'approval_step';
            ");

            if ($query_default_constraint->num_rows() > 0) {
                $row = $query_default_constraint->row();
                $default_constraint_name = $row->name;
                $this->db->query("ALTER TABLE [dbo].[legal_cases] DROP CONSTRAINT " . $default_constraint_name);
            }

            // Now, drop the column
            $this->dbforge->drop_column('legal_cases', 'approval_step');
        }

        // Add the column with the new definition (BIGINT NULL)
        $this->dbforge->add_column('legal_cases', [
            'approval_step' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => TRUE,
            ]
        ]);
		
		// Add  status column to conveyancing_instruments table
     
        if (!$this->db->field_exists('status', 'conveyancing_instruments')) {
            $this->dbforge->add_column('conveyancing_instruments', [
                'status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ],
            ]);
        }


        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop the column for rollback
        if ($this->db->field_exists('approval_step', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'approval_step');
        }

        // Revert to original definition (TINYINT NOT NULL DEFAULT 0) and re-add the default constraint.
        // This assumes the original state was TINYINT with a default of 0 and NOT NULL.
        $this->dbforge->add_column('legal_cases', [
            'approval_step' => [
                'type' => 'TINYINT',
                'constraint' => 3, // Tinyint is 0-255, constraint 3 for display width
                'null' => FALSE,
                'default' => 0, // Revert to original default
            ]
        ]);

        // Re-add the original default constraint.
        // The name of the default constraint might be dynamic, so we'll just add a new one if needed.
        // This assumes that the 'up' method successfully removed the old dynamic one.
        // However, if the down method is run directly after 'up', the column might not be re-created with the default.
        // A safer approach might be to capture the original default constraint name in 'up'
        // and reuse it in 'down', or ensure the default is set via add_column if CI supports it directly.
        // For now, assuming direct add_column handles default for new column.

        $this->db->trans_complete(); // Complete the transaction
    }
}
