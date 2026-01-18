<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Exhibit_Activities_Log_Table extends CI_Migration {

    private $table_name = 'exhibit_activities_log';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // 1. Create exhibit_activities_log table structure
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ],
            'exhibit_id' => [
                'type' => 'BIGINT',
                'null' => TRUE,
            ],
            'remarks' => [
                'type' => 'VARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'createdBy' => [
                'type' => 'BIGINT',
                'null' => TRUE,
            ],
            'createdOn' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => 'GETDATE()', // Set default in CI DB Forge
            ],
            'requires_followup' => [
                'type' => 'NVARCHAR',
                'constraint' => '3',
                'null' => TRUE,
            ],
            'tags' => [
                'type' => 'NVARCHAR',
                'constraint' => '250',
                'null' => TRUE,
            ],
            'priority' => [
                'type' => 'NVARCHAR',
                'constraint' => '10',
                'null' => TRUE,
            ],
            'note_type' => [
                'type' => 'NVARCHAR',
                'constraint' => '10',
                'null' => TRUE,
            ],
            'modifiedOn' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'modifiedBy' => [
                'type' => 'BIGINT',
                'null' => TRUE,
            ],
            'CONSTRAINT pk_exhibit_activities_log PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table($this->table_name, TRUE);

        // 2. Add Foreign Key Constraints (using raw SQL)
        
        // FK_exhibit_activities_log_createdBy (to users)
        $this->db->query("
            IF OBJECT_ID('FK_exhibit_activities_log_createdBy', 'F') IS NULL
            BEGIN
                ALTER TABLE [dbo].[{$this->table_name}] WITH CHECK ADD CONSTRAINT [FK_exhibit_activities_log_createdBy] FOREIGN KEY([createdBy])
                REFERENCES [dbo].[users] ([id])
            END
        ");

        $this->db->query("
            IF OBJECT_ID('FK_exhibit_activities_log_exhibit', 'F') IS NULL
            BEGIN
                ALTER TABLE [dbo].[{$this->table_name}] WITH CHECK ADD CONSTRAINT [FK_exhibit_activities_log_exhibit] FOREIGN KEY([exhibit_id])
                REFERENCES [dbo].[exhibit] ([id])
            END
        ");
        
        $this->db->trans_complete();
    }

    public function down()
    {
        $this->db->trans_start();

        // Drop the table (this will automatically drop related constraints)
        $this->dbforge->drop_table($this->table_name, TRUE);

        $this->db->trans_complete();
    }
}
