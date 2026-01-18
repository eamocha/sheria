<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Suspect_Arrest_Table extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Create 'suspect_arrest' table
        if (!$this->db->table_exists('suspect_arrest')) {
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
                'arrest_date' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'arrested_contact_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'arrested_gender' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'arrested_age' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'arrest_police_station' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ],
                'arrest_ob_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'arrest_case_file_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'arrest_attachments' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'arrest_remarks' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'createdOn datetime NULL DEFAULT GETDATE()', // Default handled here
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'modifiedOn datetime NULL',
                'bail_status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'arrest_location' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'place_arrested' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'arresting_officers' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'archived' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '2',
                    'null' => TRUE,
                    'default' => 'no', // Default handled here
                ],
                'CONSTRAINT pk_suspect_arrest PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('suspect_arrest', TRUE);

            // Add foreign key constraints if the referenced tables exist
            // FK_suspect_arrest_legal_cases
            if ($this->db->table_exists('legal_cases')) {
                $this->db->query("
                    ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [FK_suspect_arrest_legal_cases] FOREIGN KEY([case_id])
                    REFERENCES [dbo].[legal_cases] ([id])
                    ON DELETE CASCADE;
                ");
                $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_legal_cases];");
            }

            // FK_suspect_arrest_contacts
            if ($this->db->table_exists('contacts')) {
                $this->db->query("
                    ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [FK_suspect_arrest_contacts] FOREIGN KEY([arrested_contact_id])
                    REFERENCES [dbo].[contacts] ([id])
                    ON DELETE CASCADE;
                ");
                $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_contacts];");
            }

            // FK_suspect_arrest_attachments (assuming documents_management_system table)
            if ($this->db->table_exists('documents_management_system')) {
                $this->db->query("
                    ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [FK_suspect_arrest_attachments] FOREIGN KEY([arrest_attachments])
                    REFERENCES [dbo].[documents_management_system] ([id])
                    ON DELETE SET NULL;
                ");
                $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_attachments];");
            }

            // FK_suspect_arrest_createdBy
            if ($this->db->table_exists('users')) {
                $this->db->query("
                    ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [FK_suspect_arrest_createdBy] FOREIGN KEY([createdBy])
                    REFERENCES [dbo].[users] ([id])
                    ON DELETE NO ACTION ON UPDATE NO ACTION;
                ");
                $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_createdBy];");
            }

            // FK_suspect_arrest_modifiedBy
            if ($this->db->table_exists('users')) {
                $this->db->query("
                    ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [FK_suspect_arrest_modifiedBy] FOREIGN KEY([modifiedBy])
                    REFERENCES [dbo].[users] ([id])
                    ON DELETE NO ACTION ON UPDATE NO ACTION;
                ");
                $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [FK_suspect_arrest_modifiedBy];");
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop foreign key constraints first
        $fk_names_to_drop = [
            'FK_suspect_arrest_modifiedBy',
            'FK_suspect_arrest_createdBy',
            'FK_suspect_arrest_attachments',
            'FK_suspect_arrest_contacts',
            'FK_suspect_arrest_legal_cases',
        ];

        foreach ($fk_names_to_drop as $fk_name) {
            $query_result = $this->db->query("SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[suspect_arrest]');");
            if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
                $this->db->query("ALTER TABLE [dbo].[suspect_arrest] DROP CONSTRAINT [{$fk_name}];");
            }
        }

        // Drop the table
        if ($this->db->table_exists('suspect_arrest')) {
            $this->dbforge->drop_table('suspect_arrest', TRUE);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
