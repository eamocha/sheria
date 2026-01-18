<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Email_Templates_Table extends CI_Migration {

    private $table_name = 'email_templates';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction

        // SQL to create the table, including IDENTITY, constraints, and defaults
        $create_table_sql = "
            CREATE TABLE [dbo].[{$this->table_name}] (
                [id]                INT             IDENTITY (1, 1) NOT NULL,
                [template_key]      NVARCHAR (100)  NOT NULL,
                [template_name]     NVARCHAR (255)  NOT NULL,
                [subject]           NVARCHAR (255)  NULL,
                [body_content]      NVARCHAR (MAX)  NOT NULL,
                [is_active]         BIT             CONSTRAINT [DF_email_templates_is_active] DEFAULT ((1)) NOT NULL,
                [variable_count]    INT             NOT NULL,
                [last_modified_by]  INT             NULL,
                [updated_at]        DATETIME2 (0)   CONSTRAINT [DF_email_templates_updated_at] DEFAULT (GETDATE()) NOT NULL,
                
                -- Constraints
                CONSTRAINT [PK_email_templates] PRIMARY KEY CLUSTERED ([id] ASC),
                CONSTRAINT [UQ_email_templates_key] UNIQUE NONCLUSTERED ([template_key] ASC)
            );
        ";
        $this->db->query($create_table_sql);

        // SQL to create the non-clustered index on template_key for faster lookups
        $create_index_sql = "
            CREATE NONCLUSTERED INDEX [IX_email_templates_key]
            ON [dbo].[{$this->table_name}] ([template_key]);
        ";
        $this->db->query($create_index_sql);

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction

        // Drop the table to rollback the migration
        $this->dbforge->drop_table($this->table_name, TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}
