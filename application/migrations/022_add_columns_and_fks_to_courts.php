<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Columns_And_Fks_To_Courts extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Add new columns to the courts table ---
        // Check if column exists before adding
        if (!$this->db->field_exists('court_rank_id', 'courts')) {
            $this->dbforge->add_column('courts', [
                'court_rank_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }
        if (!$this->db->field_exists('court_region_id', 'courts')) {
            $this->dbforge->add_column('courts', [
                'court_region_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }
        if (!$this->db->field_exists('court_type_id', 'courts')) {
            $this->dbforge->add_column('courts', [
                'court_type_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }
        if (!$this->db->field_exists('court_hierarchy', 'courts')) {
            $this->dbforge->add_column('courts', [
                'court_hierarchy' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                ]
            ]);
        }

        // --- Add foreign key constraints ---

        // FK_courts_rank
        $fk_name_rank = 'FK_courts_rank';
        $check_fk_sql_rank = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_rank}' AND parent_object_id = OBJECT_ID('[dbo].[courts]');";
        $query_result_rank = $this->db->query($check_fk_sql_rank);
        $fk_exists_rank = false;
        if ($query_result_rank instanceof CI_DB_result && $query_result_rank->num_rows() > 0 && isset($query_result_rank->row()->count) && $query_result_rank->row()->count > 0) {
            $fk_exists_rank = true;
        }

        if (!$fk_exists_rank && $this->db->table_exists('court_degrees')) {
            $this->db->query("
                ALTER TABLE [dbo].[courts] WITH CHECK
                ADD CONSTRAINT [{$fk_name_rank}] FOREIGN KEY([court_rank_id])
                REFERENCES [dbo].[court_degrees] ([id])
                ON DELETE NO ACTION ON UPDATE NO ACTION; -- Changed to NO ACTION to prevent cascade cycle issues
            ");
            $this->db->query("ALTER TABLE [dbo].[courts] CHECK CONSTRAINT [{$fk_name_rank}];");
        }

        // FK_courts_region
        $fk_name_region = 'FK_courts_region';
        $check_fk_sql_region = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_region}' AND parent_object_id = OBJECT_ID('[dbo].[courts]');";
        $query_result_region = $this->db->query($check_fk_sql_region);
        $fk_exists_region = false;
        if ($query_result_region instanceof CI_DB_result && $query_result_region->num_rows() > 0 && isset($query_result_region->row()->count) && $query_result_region->row()->count > 0) {
            $fk_exists_region = true;
        }

        if (!$fk_exists_region && $this->db->table_exists('court_regions')) {
            $this->db->query("
                ALTER TABLE [dbo].[courts] WITH CHECK
                ADD CONSTRAINT [{$fk_name_region}] FOREIGN KEY([court_region_id])
                REFERENCES [dbo].[court_regions] ([id])
                ON DELETE NO ACTION ON UPDATE NO ACTION; -- Changed to NO ACTION to prevent cascade cycle issues
            ");
            $this->db->query("ALTER TABLE [dbo].[courts] CHECK CONSTRAINT [{$fk_name_region}];");
        }

        // FK_courts_type
        $fk_name_type = 'FK_courts_type';
        $check_fk_sql_type = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_type}' AND parent_object_id = OBJECT_ID('[dbo].[courts]');";
        $query_result_type = $this->db->query($check_fk_sql_type);
        $fk_exists_type = false;
        if ($query_result_type instanceof CI_DB_result && $query_result_type->num_rows() > 0 && isset($query_result_type->row()->count) && $query_result_type->row()->count > 0) {
            $fk_exists_type = true;
        }

        if (!$fk_exists_type && $this->db->table_exists('court_types')) {
            $this->db->query("
                ALTER TABLE [dbo].[courts] WITH CHECK
                ADD CONSTRAINT [{$fk_name_type}] FOREIGN KEY([court_type_id])
                REFERENCES [dbo].[court_types] ([id])
                ON DELETE NO ACTION ON UPDATE NO ACTION; -- Changed to NO ACTION to prevent cascade cycle issues
            ");
            $this->db->query("ALTER TABLE [dbo].[courts] CHECK CONSTRAINT [{$fk_name_type}];");
        }

        // --- Add Indexes for Performance ---
        $indexes = [
            'IX_courts_rank' => 'court_rank_id',
            'IX_courts_region' => 'court_region_id',
            'IX_courts_type' => 'court_type_id',
        ];

        foreach ($indexes as $idx_name => $column_name) {
            $check_idx_sql = "SELECT COUNT(*) AS count FROM sys.indexes WHERE name = '{$idx_name}' AND object_id = OBJECT_ID('[dbo].[courts]');";
            $query_result = $this->db->query($check_idx_sql);
            $idx_exists = false;
            if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
                $idx_exists = true;
            }

            if (!$idx_exists && $this->db->field_exists($column_name, 'courts')) {
                $this->db->query("CREATE INDEX {$idx_name} ON [dbo].[courts]({$column_name});");
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Indexes First ---
        $indexes_to_drop = [
            'IX_courts_type',
            'IX_courts_region',
            'IX_courts_rank',
        ];
        foreach ($indexes_to_drop as $idx_name) {
            $query_result = $this->db->query("SELECT COUNT(*) AS count FROM sys.indexes WHERE name = '{$idx_name}' AND object_id = OBJECT_ID('[dbo].[courts]');");
            if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
                $this->db->query("DROP INDEX {$idx_name} ON [dbo].[courts];");
            }
        }

        // --- Drop Foreign Key Constraints ---
        $fk_names_to_drop = [
            'FK_courts_type',
            'FK_courts_region',
            'FK_courts_rank',
        ];
        foreach ($fk_names_to_drop as $fk_name) {
            $query_result = $this->db->query("SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[courts]');");
            if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
                $this->db->query("ALTER TABLE [dbo].[courts] DROP CONSTRAINT [{$fk_name}];");
            }
        }

        // --- Drop Columns ---
        if ($this->db->field_exists('court_hierarchy', 'courts')) {
            $this->dbforge->drop_column('courts', 'court_hierarchy');
        }
        if ($this->db->field_exists('court_type_id', 'courts')) {
            $this->dbforge->drop_column('courts', 'court_type_id');
        }
        if ($this->db->field_exists('court_region_id', 'courts')) {
            $this->dbforge->drop_column('courts', 'court_region_id');
        }
        if ($this->db->field_exists('court_rank_id', 'courts')) {
            $this->dbforge->drop_column('courts', 'court_rank_id');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
