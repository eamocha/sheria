<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Case_Closure_Recommendation_Table extends CI_Migration {

	public function up()
	{
		$this->db->trans_start();

		if (!$this->db->table_exists('case_closure_recommendation')) {
			$this->dbforge->add_field([
				'id' => [
					'type' => 'BIGINT',
					'constraint' => 20,
					'auto_increment' => TRUE
				],
				'case_id' => [
					'type' => 'BIGINT',
					'constraint' => 20,
					'null' => FALSE,
				],
				'investigation_officer_recommendation' => [
					'type' => 'NVARCHAR',
					'constraint' => '250',
					'null' => TRUE,
				],
				'date_recommended' => [
					'type' => 'DATE',
					'null' => TRUE,
				],
				'approval_remarks' => [
					'type' => 'NVARCHAR',
					'constraint' => '250',
					'null' => TRUE,
				],
				'approval_date' => [
					'type' => 'DATE',
					'null' => TRUE,
				],
				'approval_status' => [
					'type' => 'NVARCHAR',
					'constraint' => '50',
					'null' => TRUE,
				],
				'approvedBy' => [
					'type' => 'BIGINT',
					'constraint' => 20,
					'null' => TRUE,
				],
				'createdOn' => [
					'type' => 'DATETIME',
					'null' => FALSE,
					'default' => 'GETDATE()', // Note: some CI drivers require raw strings for functions
				],
				'createdBy' => [
					'type' => 'BIGINT',
					'constraint' => 20,
					'null' => TRUE,
				],
				'modifiedOn' => [
					'type' => 'DATETIME',
					'null' => TRUE,
				],
				'modifiedBy' => [
					'type' => 'BIGINT',
					'constraint' => 20,
					'null' => TRUE,
				],
				'recommendation_status' => [
					'type' => 'NVARCHAR',
					'constraint' => '50',
					'null' => TRUE,
				],
			]);

			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table('case_closure_recommendation', TRUE);

			// --- Foreign Keys (Using NO ACTION to avoid Msg 1785) ---
			$this->add_foreign_key_if_not_exists('case_closure_recommendation', 'FK_CaseClosure_ApprovedBy_Users', 'approvedBy', 'users', 'id', 'NO ACTION');
			$this->add_foreign_key_if_not_exists('case_closure_recommendation', 'FK_CaseClosure_CreatedBy_Users', 'createdBy', 'users', 'id', 'NO ACTION');
			$this->add_foreign_key_if_not_exists('case_closure_recommendation', 'FK_CaseClosure_LegalCase', 'case_id', 'legal_cases', 'id', 'NO ACTION');
		}

		$this->db->trans_complete();
	}

	public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Foreign Key Constraints First ---
        $fk_names_to_drop = [
            'FK_CaseClosure_ApprovedBy_Users',
            'FK_CaseClosure_CreatedBy_Users',
            'FK_CaseClosure_LegalCase',
        ];

        foreach ($fk_names_to_drop as $fk_name) {
            $this->drop_foreign_key_if_exists('case_closure_recommendation', $fk_name);
        }

        // --- Drop the table ---
        if ($this->db->table_exists('case_closure_recommendation')) {
            $this->dbforge->drop_table('case_closure_recommendation', TRUE);
        }

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
}
