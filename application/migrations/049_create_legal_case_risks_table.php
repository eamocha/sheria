<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Legal_Case_Risks_Table extends CI_Migration {

    private $table_name = 'legal_case_risks';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // 1. Create legal_case_risks table structure
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'case_id' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'risk_category' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'riskLevel' => [
                'type' => 'NVARCHAR',
                'constraint' => '50',
                'null' => FALSE,
            ],
            'risk_type' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'details' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'mitigation' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'responsible_actor_id' => [
                'type' => 'BIGINT',
                'null' => TRUE,
            ],
            'status' => [
                'type' => 'NVARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ],
            'createdBy' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'createdOn' => [
                'type' => 'DATETIME',
                'null' => FALSE,
                'default' => 'GETDATE()',
            ],
            'CONSTRAINT pk_legal_case_risks PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table($this->table_name, TRUE);

        // 2. Add Unique Constraint UQ_legal_case_risks_case_category
        $this->db->query("
            IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'UQ_legal_case_risks_case_category' AND object_id = OBJECT_ID('{$this->table_name}'))
            BEGIN
                CREATE UNIQUE NONCLUSTERED INDEX UQ_legal_case_risks_case_category
                ON [dbo].[{$this->table_name}] (case_id ASC, risk_category ASC)
            END
        ");

        // 3. Add Foreign Key Constraints (using raw SQL for specific ON DELETE CASCADE/CHECK)
        
        // FK_legal_case_risks_case (to legal_cases, ON DELETE CASCADE)
        $this->db->query("
            IF OBJECT_ID('FK_legal_case_risks_case', 'F') IS NULL
            BEGIN
                ALTER TABLE [dbo].[{$this->table_name}] WITH CHECK ADD CONSTRAINT [FK_legal_case_risks_case] FOREIGN KEY([case_id])
                REFERENCES [dbo].[legal_cases] ([id])
                ON DELETE CASCADE
            END
        ");
        
        // FK_legal_case_risks_actor (to users)
        $this->db->query("
            IF OBJECT_ID('FK_legal_case_risks_actor', 'F') IS NULL
            BEGIN
                ALTER TABLE [dbo].[{$this->table_name}] WITH CHECK ADD CONSTRAINT [FK_legal_case_risks_actor] FOREIGN KEY([responsible_actor_id])
                REFERENCES [dbo].[users] ([id])
            END
        ");

        // FK_legal_case_risks_user (to users for createdBy)
        $this->db->query("
            IF OBJECT_ID('FK_legal_case_risks_user', 'F') IS NULL
            BEGIN
                ALTER TABLE [dbo].[{$this->table_name}] WITH CHECK ADD CONSTRAINT [FK_legal_case_risks_user] FOREIGN KEY([createdBy])
                REFERENCES [dbo].[users] ([id])
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
