<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_System_Preferences extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Data to be inserted
        $preferences = [
            ['groupName' => 'ContractDefaultValues', 'keyName' => 'AutoCreateTaskOnNewContract', 'keyValue' => 'yes'],
            ['groupName' => 'ContractDefaultValues', 'keyName' => 'taskTypeIdOnNewContract', 'keyValue' => '1'],
            ['groupName' => 'ContractDefaultValues', 'keyName' => 'EnableContractRenewalFeature', 'keyValue' => '1'],
        ];

        foreach ($preferences as $pref) {
            // Check if the record already exists before inserting to prevent duplicates
            $exists = $this->db->get_where('system_preferences', ['groupName' => $pref['groupName'], 'keyName' => $pref['keyName']])->num_rows();
            if ($exists == 0) {
                $this->db->insert('system_preferences', $pref);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Data to be deleted
        $preferences_to_delete = [
            'AutoCreateTaskOnNewContract',
            'taskTypeIdOnNewContract',
            'EnableContractRenewalFeature',
        ];

        $this->db->where_in('keyName', $preferences_to_delete);
        $this->db->delete('system_preferences');

        $this->db->trans_complete(); // Complete the transaction
    }
}
