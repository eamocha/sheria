<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_Active_Directory_Preferences extends CI_Migration {

    private $table = 'system_preferences';
    private $preferences = [
        // Base DN for the entire domain
        ['groupName' => 'ActiveDirectory', 'keyName' => 'baseDn', 'keyValue' => 'eg Dc=sheria360,DC=co,DC=ke'],
        // Specific DN for searching users
        ['groupName' => 'ActiveDirectory', 'keyName' => 'UserSearchBaseDN', 'keyValue' => 'eg OU=Users,Dc=sheria360,DC=co,DC=ke'],
    ];

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Insert new Active Directory preference keys if they do not already exist
        foreach ($this->preferences as $pref) {
            // Check for existence based on groupName and keyName to ensure idempotency
            $exists = $this->db->get_where($this->table, [
                'groupName' => $pref['groupName'],
                'keyName' => $pref['keyName']
            ])->num_rows() > 0;

            if (!$exists) {
                $this->db->insert($this->table, $pref);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Delete the Active Directory preference keys
        foreach ($this->preferences as $pref) {
            $this->db->delete($this->table, [
                'groupName' => $pref['groupName'],
                'keyName' => $pref['keyName']
            ]);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
