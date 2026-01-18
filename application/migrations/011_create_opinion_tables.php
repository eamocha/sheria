<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Opinion_Tables extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Create Independent Core Tables ---

        // opinion_document_status
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'CONSTRAINT pk_opinion_document_status PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('opinion_document_status', TRUE);

        // opinion_document_type
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'CONSTRAINT pk_opinion_document_type PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('opinion_document_type', TRUE);

        // opinion_statuses
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ],
            'category' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => FALSE,
                'default' => 'in progress',
            ],
            'isGlobal' => [
                'type' => 'TINYINT',
                'null' => FALSE,
                'default' => 1,
            ],
            'CONSTRAINT pk_opinion_statuses PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('opinion_statuses', TRUE);

        // opinion_types
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'CONSTRAINT pk_opinion_types PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('opinion_types', TRUE);

        // opinion_locations
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ],
            'CONSTRAINT pk_opinion_locations PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('opinion_locations', TRUE);

        // --- Main opinion table: opinions ---
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'title' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ],
            'legal_case_id' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'contract_id' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'stage' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'assigned_to' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => FALSE,
            ],
            'private' => [
                'type' => 'CHAR',
                'constraint' => '3',
                'null' => TRUE,
                'default' => NULL,
            ],
            'priority' => [
                'type' => 'NVARCHAR',
                'constraint' => '8',
                'null' => FALSE,
                'default' => 'medium',
            ],
            'opinion_location_id' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'detailed_info' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX', // TEXT equivalent for SQL Server
                'null' => TRUE,
            ],
            'opinion_status_id' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'opinion_type_id' => [
                'type' => 'BIGINT',
                'null' => FALSE,
            ],
            'estimated_effort' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => TRUE,
                'default' => NULL,
            ],
            'createdOn datetime NULL DEFAULT NULL', // Explicit default NULL
            'createdBy' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'modifiedOn datetime NULL DEFAULT NULL', // Explicit default NULL
            'modifiedBy' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'archived' => [
                'type' => 'NVARCHAR',
                'constraint' => '3',
                'null' => FALSE,
                'default' => 'no',
            ],
            'hideFromBoard' => [
                'type' => 'NVARCHAR',
                'constraint' => '3',
                'null' => TRUE,
                'default' => NULL,
            ],
            'reporter' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'workflow' => [
                'type' => 'BIGINT',
                'null' => FALSE,
                'default' => 1,
            ],
            'legal_question' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX', // TEXT equivalent for SQL Server
                'null' => TRUE,
            ],
            'opinion_file' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'category' => [
                'type' => 'NVARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'default' => 'opinions',
            ],
            'background_info' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX', // TEXT equivalent for SQL Server
                'null' => TRUE,
            ],
            'requester' => [
                'type' => 'BIGINT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'channel' => [
                'type' => 'NVARCHAR',
                'constraint' => '5',
                'null' => TRUE,
                'default' => NULL,
            ],
            'is_visible_to_cp' => [
                'type' => 'BIT',
                'null' => TRUE,
                'default' => NULL,
            ],
            'CONSTRAINT pk_opinions PRIMARY KEY (id)',
        ]);
        $this->dbforge->create_table('opinions', TRUE);

        // Add CHECK Constraints using raw SQL
      //  $this->db->query("ALTER TABLE [dbo].[opinions] ADD CONSTRAINT [CHK_opinions_archived] CHECK ([archived] IN ('no', 'yes'));");
     //   $this->db->query("ALTER TABLE [dbo].[opinions] ADD CONSTRAINT [CHK_opinions_priority] CHECK ([priority] IN ('low', 'medium', 'high', 'critical'));");


        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop tables in reverse order of creation
        $this->dbforge->drop_table('opinions', TRUE);
        $this->dbforge->drop_table('opinion_locations', TRUE);
        $this->dbforge->drop_table('opinion_types', TRUE);
        $this->dbforge->drop_table('opinion_statuses', TRUE);
        $this->dbforge->drop_table('opinion_document_type', TRUE);
        $this->dbforge->drop_table('opinion_document_status', TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}
