<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_Contract_Default_Preferences extends CI_Migration {

    private $table = 'system_preferences';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Data to be inserted
        $data = [
            [
                'groupName' => 'ContractDefaultValues',
                'keyName' => 'AutoCreateTaskOnNewContract',
                'keyValue' => 'yes'
            ],
            [
                'groupName' => 'ContractDefaultValues',
                'keyName' => 'taskTypeIdOnNewContract',
                'keyValue' => '1'
            ],
            [
                'groupName' => 'ContractDefaultValues',
                'keyName' => 'EnableContractRenewalFeature',
                'keyValue' => '1'
            ]
        ];

        foreach ($data as $row) {
            // Check if the preference already exists before inserting
            $exists = $this->db->get_where($this->table, [
                'groupName' => $row['groupName'],
                'keyName' => $row['keyName']
            ])->num_rows();

            if ($exists == 0) {
                $this->db->insert($this->table, $row);
            } else {
                // If it exists, ensure the keyValue is updated if necessary (optional step)
                // $this->db->where('groupName', $row['groupName'])->where('keyName', $row['keyName'])->update($this->table, ['keyValue' => $row['keyValue']]);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        $keys_to_delete = [
            'AutoCreateTaskOnNewContract',
            'taskTypeIdOnNewContract',
            'EnableContractRenewalFeature'
        ];

        // Delete the inserted preferences
        $this->db->where('groupName', 'ContractDefaultValues')
                 ->where_in('keyName', $keys_to_delete)
                 ->delete($this->table);

        $this->db->trans_complete(); // Complete the transaction
    }
}
