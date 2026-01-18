<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Disable_Conveyancing_Date_Constraint extends CI_Migration {

    private $table_name = 'conveyancing_stage_progress';
    private $constraint_name = 'CK_conveyancing_date_logic';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Disable the CHECK constraint to allow data manipulation that might temporarily violate it.
        // This is typically done before a major data import or update.
        $sql = "ALTER TABLE [dbo].[{$this->table_name}] NOCHECK CONSTRAINT [{$this->constraint_name}]";
        
        // Execute the SQL statement only if the constraint exists (optional safety check)
        $check_constraint_exists_sql = "
            SELECT 1
            FROM sys.check_constraints 
            WHERE parent_object_id = OBJECT_ID('{$this->table_name}') 
            AND name = '{$this->constraint_name}'
        ";

        if ($this->db->query($check_constraint_exists_sql)->num_rows() > 0) {
            $this->db->query($sql);
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // The DOWN method should re-enable the constraint.
        // Re-enabling performs a check on existing data, so ensure data integrity is restored before this is run.
        $sql = "ALTER TABLE [dbo].[{$this->table_name}] CHECK CONSTRAINT [{$this->constraint_name}]";

        // Execute the SQL statement only if the constraint exists (optional safety check)
        $check_constraint_exists_sql = "
            SELECT 1
            FROM sys.check_constraints 
            WHERE parent_object_id = OBJECT_ID('{$this->table_name}') 
            AND name = '{$this->constraint_name}'
        ";

        if ($this->db->query($check_constraint_exists_sql)->num_rows() > 0) {
            $this->db->query($sql);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
