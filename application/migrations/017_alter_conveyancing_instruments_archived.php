<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Conveyancing_Instruments_Archived extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Check if the column exists and has a default constraint, then drop it
        if ($this->db->field_exists('archived', 'conveyancing_instruments')) {
            $sql = "SELECT d.name
                    FROM sys.default_constraints d
                    JOIN sys.columns c ON d.parent_object_id = c.object_id AND d.parent_column_id = c.column_id
                    WHERE d.parent_object_id = OBJECT_ID('conveyancing_instruments') AND c.name = 'archived'";

            $query = $this->db->query($sql);

            if ($query->num_rows() > 0)
            {
                $constraint_name = $query->row()->name;
                $this->db->query("ALTER TABLE conveyancing_instruments DROP CONSTRAINT [{$constraint_name}]");
            }

            // Now that the constraint is gone, we can drop the column
            $this->dbforge->drop_column('conveyancing_instruments', 'archived');
        }

        // Add the column with the new definition
        $this->dbforge->add_column('conveyancing_instruments', [
            'archived' => [
                'type' => 'NVARCHAR',
                'constraint' => '3',
                'null' => FALSE,
                'default' => 'no',
            ]
        ]);

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Dynamically find and drop the default constraint on the 'archived' column
        $sql = "SELECT d.name
                FROM sys.default_constraints d
                JOIN sys.columns c ON d.parent_object_id = c.object_id AND d.parent_column_id = c.column_id
                WHERE d.parent_object_id = OBJECT_ID('conveyancing_instruments') AND c.name = 'archived'";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
        {
            $constraint_name = $query->row()->name;
            $this->db->query("ALTER TABLE conveyancing_instruments DROP CONSTRAINT [{$constraint_name}]");
        }

        // Drop the 'archived' column
        if ($this->db->field_exists('archived', 'conveyancing_instruments')) {
            $this->dbforge->drop_column('conveyancing_instruments', 'archived');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
