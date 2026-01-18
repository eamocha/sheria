<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_Correspondence_Lookup_Data extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Seed correspondence_document_types ---
        $document_types = [
            ['name' => 'Letter', 'description' => 'General communication letter'],
            ['name' => 'Email', 'description' => 'Electronic mail correspondence'],
            ['name' => 'Memo', 'description' => 'Internal memorandum'],
            ['name' => 'Report', 'description' => 'Detailed official report'],
            ['name' => 'Fax', 'description' => 'Facsimile communication'],
        ];
        foreach ($document_types as $data) {
            $this->db->where('name', $data['name']);
            $query = $this->db->get('correspondence_document_types');
            if ($query->num_rows() == 0) {
                $this->db->insert('correspondence_document_types', $data);
            }
        }

        // --- Seed correspondence_statuses ---
        $statuses = [
            ['name' => 'Draft'],
            ['name' => 'Sent'],
            ['name' => 'Received'],
            ['name' => 'Closed'],
            ['name' => 'Archived'],
            ['name' => 'Pending Action'],
        ];
        foreach ($statuses as $data) {
            $this->db->where('name', $data['name']);
            $query = $this->db->get('correspondence_statuses');
            if ($query->num_rows() == 0) {
                $this->db->insert('correspondence_statuses', $data);
            }
        }

        // --- Seed correspondence_types ---
        $correspondence_types = [
            ['name' => 'Internal Communication'],
            ['name' => 'External Inquiry'],
            ['name' => 'Client Communication'],
            ['name' => 'Legal Notice'],
            ['name' => 'Administrative'],
        ];
        foreach ($correspondence_types as $data) {
            $this->db->where('name', $data['name']);
            $query = $this->db->get('correspondence_types');
            if ($query->num_rows() == 0) {
                $this->db->insert('correspondence_types', $data);
            }
        }

        // --- Seed correspondence_workflow_steps ---
        // You'll need to know the IDs of correspondence_types to link them.
        // For simplicity, this example assumes the IDs are 1, 2, 3 etc. for the above seeded types.
        // In a real application, you might query for the IDs first or use a more robust seeding strategy.
        $internal_comm_id = $this->db->get_where('correspondence_types', ['name' => 'Internal Communication'])->row()->id ?? null;
        $external_inquiry_id = $this->db->get_where('correspondence_types', ['name' => 'External Inquiry'])->row()->id ?? null;

        $workflow_steps = [];
        if ($internal_comm_id) {
            $workflow_steps[] = [
                'name' => 'Draft Internal Memo',
                'correspondence_type_id' => $internal_comm_id,
                'sequence_order' => 10,
                'comment' => 'Initial draft of internal communication',
                'category' => 'Drafting'
            ];
            $workflow_steps[] = [
                'name' => 'Internal Review',
                'correspondence_type_id' => $internal_comm_id,
                'sequence_order' => 20,
                'comment' => 'Reviewed by relevant department',
                'category' => 'Review'
            ];
        }
        if ($external_inquiry_id) {
            $workflow_steps[] = [
                'name' => 'Receive Inquiry',
                'correspondence_type_id' => $external_inquiry_id,
                'sequence_order' => 10,
                'comment' => 'External inquiry received',
                'category' => 'Reception'
            ];
            $workflow_steps[] = [
                'name' => 'Assign to Team',
                'correspondence_type_id' => $external_inquiry_id,
                'sequence_order' => 20,
                'comment' => 'Inquiry assigned for action',
                'category' => 'Assignment'
            ];
            $workflow_steps[] = [
                'name' => 'Respond to Inquiry',
                'correspondence_type_id' => $external_inquiry_id,
                'sequence_order' => 30,
                'comment' => 'Official response dispatched',
                'category' => 'Response'
            ];
        }

        foreach ($workflow_steps as $data) {
            $this->db->where('name', $data['name']);
            $this->db->where('correspondence_type_id', $data['correspondence_type_id']); // Assuming name and type_id are unique
            $query = $this->db->get('correspondence_workflow_steps');
            if ($query->num_rows() == 0) {
                $this->db->insert('correspondence_workflow_steps', $data);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Remove seeded data in reverse order of dependencies (if applicable)
        // Note: For workflow_steps, simply emptying is fine if there are no other FKs pointing to them.
        $this->db->empty_table('correspondence_workflow_steps');
        $this->db->empty_table('correspondence_types');
        $this->db->empty_table('correspondence_statuses');
        $this->db->empty_table('correspondence_document_types');

        $this->db->trans_complete(); // Complete the transaction
    }
}
