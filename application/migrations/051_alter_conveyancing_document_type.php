<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Conveyancing_Document_Type extends CI_Migration {

    private $table_name = 'conveyancing_document_type';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // 1. RENAME 'addedon' to 'createdOn'
        // Using raw query for sp_rename (SQL Server specific) with checks for idempotency.
        if ($this->db->field_exists('addedon', $this->table_name) && !$this->db->field_exists('createdOn', $this->table_name)) {
            $this->db->query("EXEC sp_rename '{$this->table_name}.addedon', 'createdOn', 'COLUMN'");
        }

        // 2. ADD new columns: modifiedOn, createdBy, modifiedBy
        
        // Add modifiedOn
        if (!$this->db->field_exists('modifiedOn', $this->table_name)) {
            $this->dbforge->add_column($this->table_name, [
                'modifiedOn' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ]
            ]);
        }
        
        // Add createdBy
        if (!$this->db->field_exists('createdBy', $this->table_name)) {
            $this->dbforge->add_column($this->table_name, [
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }
        
        // Add modifiedBy
        if (!$this->db->field_exists('modifiedBy', $this->table_name)) {
            $this->dbforge->add_column($this->table_name, [
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // 1. Revert column rename: 'createdOn' back to 'addedon'
        if ($this->db->field_exists('createdOn', $this->table_name) && !$this->db->field_exists('addedon', $this->table_name)) {
             $this->db->query("EXEC sp_rename '{$this->table_name}.createdOn', 'addedon', 'COLUMN'");
        }

        // 2. Drop the newly added columns
        if ($this->db->field_exists('modifiedOn', $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, 'modifiedOn');
        }
        
        if ($this->db->field_exists('createdBy', $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, 'createdBy');
        }
        
        if ($this->db->field_exists('modifiedBy', $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, 'modifiedBy');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
