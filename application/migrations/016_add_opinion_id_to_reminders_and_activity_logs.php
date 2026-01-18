<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Opinion_Id_To_Reminders_And_Activity_Logs extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Add 'opinion_id' column to 'reminders' table ---
        if (!$this->db->field_exists('opinion_id', 'reminders')) {
            $this->dbforge->add_column('reminders', [
                'opinion_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }

        // --- Add 'opinion_id' column to 'user_activity_logs' table ---
        if (!$this->db->field_exists('opinion_id', 'user_activity_logs')) {
            $this->dbforge->add_column('user_activity_logs', [
                'opinion_id' => [
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

        // --- Drop 'opinion_id' column from 'reminders' table ---
        if ($this->db->field_exists('opinion_id', 'reminders')) {
            $this->dbforge->drop_column('reminders', 'opinion_id');
        }

        // --- Drop 'opinion_id' column from 'user_activity_logs' table ---
        if ($this->db->field_exists('opinion_id', 'user_activity_logs')) {
            $this->dbforge->drop_column('user_activity_logs', 'opinion_id');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
