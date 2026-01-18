<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_Opinion_Base_Data extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Seed opinion_document_status ---
        $document_statuses = [
            // Note: The opinion_document_status table currently only has 'id'.
            // If you intend for it to have a 'name' column, you would need to alter the table first.
            // For now, I'll add placeholder data, assuming a 'name' column will be added later.
            // If it truly only has an ID, then seeding directly isn't meaningful without context.
            // Assuming for seeding purposes, it *should* have a name like other status tables.
            // If the table definition is strictly ID-only, these inserts will fail.
            ['name' => 'Draft'],
            ['name' => 'Final'],
            ['name' => 'Approved'],
            ['name' => 'Rejected'],
            ['name' => 'Published'],
            ['name' => 'Archived'],
        ];
        // Check if the 'name' column exists before trying to insert into it
        if ($this->db->field_exists('name', 'opinion_document_status')) {
            foreach ($document_statuses as $data) {
                $this->db->where('name', $data['name']);
                $query = $this->db->get('opinion_document_status');
                if ($query->num_rows() == 0) {
                    $this->db->insert('opinion_document_status', $data);
                }
            }
        } else {
            log_message('info', 'Skipping seeding for opinion_document_status: "name" column does not exist.');
        }


        // --- Seed opinion_document_type ---
        $document_types = [
            // Similar note as above: Assuming a 'name' column will be present.
            ['name' => 'Legal Opinion'],
            ['name' => 'Advisory Note'],
            ['name' => 'Research Paper'],
            ['name' => 'Memo'],
            ['name' => 'Report'],
        ];
        if ($this->db->field_exists('name', 'opinion_document_type')) {
            foreach ($document_types as $data) {
                $this->db->where('name', $data['name']);
                $query = $this->db->get('opinion_document_type');
                if ($query->num_rows() == 0) {
                    $this->db->insert('opinion_document_type', $data);
                }
            }
        } else {
            log_message('info', 'Skipping seeding for opinion_document_type: "name" column does not exist.');
        }


        // --- Seed opinion_statuses ---
        $statuses = [
            ['name' => 'New Request', 'category' => 'in progress', 'isGlobal' => 1],
            ['name' => 'Assigned', 'category' => 'in progress', 'isGlobal' => 1],
            ['name' => 'Drafting', 'category' => 'in progress', 'isGlobal' => 1],
            ['name' => 'Review', 'category' => 'in progress', 'isGlobal' => 1],
            ['name' => 'Approved', 'category' => 'completed', 'isGlobal' => 1],
            ['name' => 'Rejected', 'category' => 'closed', 'isGlobal' => 1],
            ['name' => 'Published', 'category' => 'completed', 'isGlobal' => 1],
            ['name' => 'On Hold', 'category' => 'pending', 'isGlobal' => 1],
        ];
        foreach ($statuses as $data) {
            $this->db->where('name', $data['name']);
            $query = $this->db->get('opinion_statuses');
            if ($query->num_rows() == 0) {
                $this->db->insert('opinion_statuses', $data);
            }
        }

        // --- Seed opinion_types ---
        $opinion_types = [
            // Similar note as above: Assuming a 'name' column will be present for meaningful seeding.
            ['name' => 'General Opinion'],
            ['name' => 'Specific Opinion'],
            ['name' => 'Policy Opinion'],
            ['name' => 'Regulatory Opinion'],
        ];
        if ($this->db->field_exists('name', 'opinion_types')) {
            foreach ($opinion_types as $data) {
                $this->db->where('name', $data['name']);
                $query = $this->db->get('opinion_types');
                if ($query->num_rows() == 0) {
                    $this->db->insert('opinion_types', $data);
                }
            }
        } else {
            log_message('info', 'Skipping seeding for opinion_types: "name" column does not exist.');
        }


        // --- Seed opinion_locations ---
        $locations = [
            ['name' => 'Head Office Legal Dept'],
            ['name' => 'Branch Office Nairobi'],
            ['name' => 'External Counsel'],
            ['name' => 'Archive Storage'],
            ['name' => 'Digital Repository'],
        ];
        foreach ($locations as $data) {
            $this->db->where('name', $data['name']);
            $query = $this->db->get('opinion_locations');
            if ($query->num_rows() == 0) {
                $this->db->insert('opinion_locations', $data);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        $this->db->empty_table('opinion_locations');
        $this->db->empty_table('opinion_types');
        $this->db->empty_table('opinion_statuses');
        $this->db->empty_table('opinion_document_type');
        $this->db->empty_table('opinion_document_status');

        $this->db->trans_complete(); // Complete the transaction
    }
}
