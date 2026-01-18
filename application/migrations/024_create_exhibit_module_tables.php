<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Exhibit_Module_Tables extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Create reference/lookup tables with no dependencies ---

        // exhibit_document_types
        if (!$this->db->table_exists('exhibit_document_types')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'name' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => FALSE,
                ],
                'CONSTRAINT pk_exhibit_document_types PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('exhibit_document_types', TRUE);
        }

        // exhibit_document_statuses
        if (!$this->db->table_exists('exhibit_document_statuses')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'name' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => FALSE,
                ],
                'CONSTRAINT pk_exhibit_document_statuses PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('exhibit_document_statuses', TRUE);
        }

        // --- 2. Create exhibit_locations table ---
        if (!$this->db->table_exists('exhibit_locations')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'name' => [
                    'type' => 'NVARCHAR', // Changed from VARCHAR to NVARCHAR for consistency
                    'constraint' => '255',
                    'null' => FALSE,
                ],
                'longitude' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'latitude' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'description' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'createdOn datetime NULL',
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'modifiedOn datetime NULL',
                'CONSTRAINT pk_exhibit_locations PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('exhibit_locations', TRUE);

            // Add unique constraint for name
            $this->db->query("
                IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'UQ_exhibit_locations_name' AND object_id = OBJECT_ID('[dbo].[exhibit_locations]'))
                ALTER TABLE [dbo].[exhibit_locations]
                ADD CONSTRAINT [UQ_exhibit_locations_name] UNIQUE NONCLUSTERED ([name] ASC);
            ");
        }

        // --- 3. Create exhibit table ---
        if (!$this->db->table_exists('exhibit')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'case_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'exhibit_label' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ],
                'description' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => FALSE,
                ],
                'temporary_removals' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'manner_of_disposal' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'date_received' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'date_approved_for_disposal' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'date_disposed' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'createdOn datetime NOT NULL DEFAULT GETDATE()',
                'modifiedOn datetime NULL DEFAULT GETDATE()', // Default handled here
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'associated_party_type' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '10',
                    'null' => TRUE,
                ],
                'exhibit_status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '250',
                    'null' => TRUE,
                ],
                'officer_remarks' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'officers_involved_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'associated_party' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'pickup_location_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'current_location_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'reason_for_temporary' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '250',
                    'null' => TRUE,
                ],
                'disposal_remarks' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'status_on_pickup' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '250',
                    'null' => TRUE,
                ],
                'archived' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '3',
                    'null' => TRUE,
                ],
                'CONSTRAINT pk_exhibit PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('exhibit', TRUE);
        }

        // --- 4. Create junction tables that reference the above tables ---

        // exhibit_document
        if (!$this->db->table_exists('exhibit_document')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'exhibit_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'document' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'CONSTRAINT pk_exhibit_document PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('exhibit_document', TRUE);
        }

        // exhibit_chain_of_movement
        if (!$this->db->table_exists('exhibit_chain_of_movement')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'transfer_from_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'transfer_to_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'purpose' => [
                    'type' => 'NVARCHAR', // Changed from VARCHAR to NVARCHAR
                    'constraint' => '255',
                    'null' => TRUE,
                ],
                'remarks' => [
                    'type' => 'NVARCHAR', // Changed from VARCHAR to NVARCHAR
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'action_date_time' => [
                    'type' => 'DATETIME',
                    'null' => FALSE,
                ],
                'officer_receiving' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'createdOn datetime NULL DEFAULT GETDATE()', // Default handled here
                'exhibit_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'condition_check' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                ],
                'modifiedOn datetime NULL',
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'CONSTRAINT pk_exhibit_chain_of_movement PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('exhibit_chain_of_movement', TRUE);
        }

        // --- 5. Add all foreign key constraints ---

        // Exhibit constraints
        $fk_definitions_exhibit = [
            'fk_case_exhibit_case' => ['table' => 'exhibit', 'column' => 'case_id', 'ref_table' => 'legal_cases', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'], // Assuming NO ACTION to prevent cascade cycles
            'fk_case_exhibit_createdBy' => ['table' => 'exhibit', 'column' => 'createdBy', 'ref_table' => 'users', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            'fk_case_exhibit_modifiedBy' => ['table' => 'exhibit', 'column' => 'modifiedBy', 'ref_table' => 'users', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            'fk_exhibit_pickup_location' => ['table' => 'exhibit', 'column' => 'pickup_location_id', 'ref_table' => 'exhibit_locations', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'], // Changed to NO ACTION
            'fk_exhibit_current_location' => ['table' => 'exhibit', 'column' => 'current_location_id', 'ref_table' => 'exhibit_locations', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'], // Changed to NO ACTION
            // Add FK for officers_involved_id if it references 'users' or another table
            // 'fk_exhibit_officers_involved' => ['table' => 'exhibit', 'column' => 'officers_involved_id', 'ref_table' => 'users', 'ref_column' => 'id', 'on_delete' => 'SET NULL', 'on_update' => 'NO ACTION'],
            // Add FK for associated_party if it references 'contacts' or 'companies' (polymorphic, might need a view or different handling)
        ];

        foreach ($fk_definitions_exhibit as $fk_name => $props) {
            $this->add_foreign_key_if_not_exists($props['table'], $fk_name, $props['column'], $props['ref_table'], $props['ref_column'], $props['on_delete'], $props['on_update']);
        }

        // Exhibit_document constraints
        $fk_definitions_exhibit_document = [
            'fk_exhibit_document_1' => ['table' => 'exhibit_document', 'column' => 'exhibit_id', 'ref_table' => 'exhibit', 'ref_column' => 'id', 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            'fk_exhibit_document_2' => ['table' => 'exhibit_document', 'column' => 'document', 'ref_table' => 'documents_management_system', 'ref_column' => 'id', 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
        ];

        foreach ($fk_definitions_exhibit_document as $fk_name => $props) {
            $this->add_foreign_key_if_not_exists($props['table'], $fk_name, $props['column'], $props['ref_table'], $props['ref_column'], $props['on_delete'], $props['on_update']);
        }

        // Exhibit_chain_of_movement constraints
        $fk_definitions_chain = [
            'fk_chain_createdBy' => ['table' => 'exhibit_chain_of_movement', 'column' => 'createdBy', 'ref_table' => 'users', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            'fk_chain_transfer_from' => ['table' => 'exhibit_chain_of_movement', 'column' => 'transfer_from_id', 'ref_table' => 'exhibit_locations', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            'fk_chain_transfer_to' => ['table' => 'exhibit_chain_of_movement', 'column' => 'transfer_to_id', 'ref_table' => 'exhibit_locations', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            'fk_chain_officer_receiving' => ['table' => 'exhibit_chain_of_movement', 'column' => 'officer_receiving', 'ref_table' => 'users', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
            'fk_chain_exhibit_id' => ['table' => 'exhibit_chain_of_movement', 'column' => 'exhibit_id', 'ref_table' => 'exhibit', 'ref_column' => 'id', 'on_delete' => 'CASCADE', 'on_update' => 'CASCADE'],
            'fk_chain_modifiedBy' => ['table' => 'exhibit_chain_of_movement', 'column' => 'modifiedBy', 'ref_table' => 'users', 'ref_column' => 'id', 'on_delete' => 'NO ACTION', 'on_update' => 'NO ACTION'],
        ];

        foreach ($fk_definitions_chain as $fk_name => $props) {
            $this->add_foreign_key_if_not_exists($props['table'], $fk_name, $props['column'], $props['ref_table'], $props['ref_column'], $props['on_delete'], $props['on_update']);
        }

        // --- 6. Add Indexes for Performance ---
        $indexes = [
            'IX_exhibit_case_id' => ['table' => 'exhibit', 'column' => 'case_id'],
            'IX_exhibit_createdBy' => ['table' => 'exhibit', 'column' => 'createdBy'],
            'IX_exhibit_modifiedBy' => ['table' => 'exhibit', 'column' => 'modifiedBy'],
            'IX_exhibit_pickup_location' => ['table' => 'exhibit', 'column' => 'pickup_location_id'],
            'IX_exhibit_current_location' => ['table' => 'exhibit', 'column' => 'current_location_id'],
            'IX_exhibit_document_exhibit_id' => ['table' => 'exhibit_document', 'column' => 'exhibit_id'],
            'IX_exhibit_document_document_id' => ['table' => 'exhibit_document', 'column' => 'document'],
            'IX_chain_exhibit_id' => ['table' => 'exhibit_chain_of_movement', 'column' => 'exhibit_id'],
            'IX_chain_transfer_from_id' => ['table' => 'exhibit_chain_of_movement', 'column' => 'transfer_from_id'],
            'IX_chain_transfer_to_id' => ['table' => 'exhibit_chain_of_movement', 'column' => 'transfer_to_id'],
            'IX_chain_officer_receiving' => ['table' => 'exhibit_chain_of_movement', 'column' => 'officer_receiving'],
            'IX_chain_createdBy' => ['table' => 'exhibit_chain_of_movement', 'column' => 'createdBy'],
            'IX_chain_modifiedBy' => ['table' => 'exhibit_chain_of_movement', 'column' => 'modifiedBy'],
        ];

        foreach ($indexes as $idx_name => $props) {
            $this->add_index_if_not_exists($props['table'], $idx_name, $props['column']);
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Indexes First ---
        $indexes_to_drop = [
            'IX_chain_modifiedBy',
            'IX_chain_createdBy',
            'IX_chain_officer_receiving',
            'IX_chain_transfer_to_id',
            'IX_chain_transfer_from_id',
            'IX_chain_exhibit_id',
            'IX_exhibit_document_document_id',
            'IX_exhibit_document_exhibit_id',
            'IX_exhibit_current_location',
            'IX_exhibit_pickup_location',
            'IX_exhibit_modifiedBy',
            'IX_exhibit_createdBy',
            'IX_exhibit_case_id',
            'UQ_exhibit_locations_name', // Unique constraint is also an index
        ];

        foreach ($indexes_to_drop as $idx_name) {
            // Determine the table for the index
            $table_name = '';
            if (strpos($idx_name, 'IX_exhibit_document_') === 0) $table_name = 'exhibit_document';
            else if (strpos($idx_name, 'IX_chain_') === 0) $table_name = 'exhibit_chain_of_movement';
            else if (strpos($idx_name, 'IX_exhibit_') === 0) $table_name = 'exhibit';
            else if ($idx_name === 'UQ_exhibit_locations_name') $table_name = 'exhibit_locations';

            if (!empty($table_name)) {
                $this->drop_index_if_exists($table_name, $idx_name);
            }
        }

        // --- Drop Foreign Key Constraints ---
        $fk_names_to_drop = [
            'fk_chain_modifiedBy',
            'fk_chain_createdBy',
            'fk_chain_exhibit_id',
            'fk_chain_officer_receiving',
            'fk_chain_transfer_to',
            'fk_chain_transfer_from',
            'fk_exhibit_document_2',
            'fk_exhibit_document_1',
            'fk_case_exhibit_modifiedBy',
            'fk_case_exhibit_createdBy',
            'fk_case_exhibit_case',
            'fk_exhibit_pickup_location',
            'fk_exhibit_current_location',
        ];

        foreach ($fk_names_to_drop as $fk_name) {
            $parent_table = '';
            if (strpos($fk_name, 'fk_exhibit_document_') === 0) $parent_table = 'exhibit_document';
            else if (strpos($fk_name, 'fk_chain_') === 0) $parent_table = 'exhibit_chain_of_movement';
            else if (strpos($fk_name, 'fk_case_exhibit_') === 0 || strpos($fk_name, 'fk_exhibit_') === 0) $parent_table = 'exhibit';

            if (!empty($parent_table)) {
                $this->drop_foreign_key_if_exists($parent_table, $fk_name);
            }
        }

        // --- Drop Tables (in reverse order of creation, considering dependencies) ---
        $this->dbforge->drop_table('exhibit_chain_of_movement', TRUE);
        $this->dbforge->drop_table('exhibit_document', TRUE);
        $this->dbforge->drop_table('exhibit', TRUE);
        $this->dbforge->drop_table('exhibit_locations', TRUE);
        $this->dbforge->drop_table('exhibit_document_statuses', TRUE);
        $this->dbforge->drop_table('exhibit_document_types', TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }

    /**
     * Helper function to add foreign key if it doesn't exist.
     */
    private function add_foreign_key_if_not_exists($table, $fk_name, $column, $ref_table, $ref_column, $on_delete, $on_update = 'NO ACTION')
    {
        $check_fk_sql = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[{$table}]');";
        $query_result = $this->db->query($check_fk_sql);
        $fk_exists = false;
        if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
            $fk_exists = true;
        }

        if (!$fk_exists && $this->db->table_exists($table) && $this->db->field_exists($column, $table) && $this->db->table_exists($ref_table)) {
            $this->db->query("
                ALTER TABLE [dbo].[{$table}] WITH CHECK ADD CONSTRAINT [{$fk_name}] FOREIGN KEY([{$column}])
                REFERENCES [dbo].[{$ref_table}] ([{$ref_column}])
                ON DELETE {$on_delete} ON UPDATE {$on_update};
            ");
            $this->db->query("ALTER TABLE [dbo].[{$table}] CHECK CONSTRAINT [{$fk_name}];");
        }
    }

    /**
     * Helper function to drop foreign key if it exists.
     */
    private function drop_foreign_key_if_exists($table, $fk_name)
    {
        $check_fk_sql = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[{$table}]');";
        $query_result = $this->db->query($check_fk_sql);
        if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
            $this->db->query("ALTER TABLE [dbo].[{$table}] DROP CONSTRAINT [{$fk_name}];");
        }
    }

    /**
     * Helper function to add index if it doesn't exist.
     */
    private function add_index_if_not_exists($table, $idx_name, $column)
    {
        $check_idx_sql = "SELECT COUNT(*) AS count FROM sys.indexes WHERE name = '{$idx_name}' AND object_id = OBJECT_ID('[dbo].[{$table}]');";
        $query_result = $this->db->query($check_idx_sql);
        $idx_exists = false;
        if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
            $idx_exists = true;
        }

        if (!$idx_exists && $this->db->table_exists($table) && $this->db->field_exists($column, $table)) {
            $this->db->query("CREATE INDEX {$idx_name} ON [dbo].[{$table}]({$column});");
        }
    }

    /**
     * Helper function to drop index if it exists.
     */
    private function drop_index_if_exists($table, $idx_name)
    {
        $check_idx_sql = "SELECT COUNT(*) AS count FROM sys.indexes WHERE name = '{$idx_name}' AND object_id = OBJECT_ID('[dbo].[{$table}]');";
        $query_result = $this->db->query($check_idx_sql);
        if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
            $this->db->query("DROP INDEX {$idx_name} ON [dbo].[{$table}];");
        }
    }
}
