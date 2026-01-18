<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_Conveyancing_Data extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Seed conveyancing_document_status
        $document_statuses = [
            ['name' => 'Draft'],
            ['name' => 'Final'],
            ['name' => 'Approved'],
            ['name' => 'Rejected'],
            ['name' => 'Signed'],
            ['name' => 'Filed'],
        ];
        foreach ($document_statuses as $status_data) {
            $this->db->where('name', $status_data['name']);
            $query = $this->db->get('conveyancing_document_status');
            if ($query->num_rows() == 0) {
                $this->db->insert('conveyancing_document_status', $status_data);
            }
        }

        // Seed conveyancing_document_type
        $document_types = [
            ['name' => 'Sale Agreement'],
            ['name' => 'Deed of Transfer'],
            ['name' => 'Title Deed'],
            ['name' => 'Charge Instrument'],
            ['name' => 'Certificate of Lease'],
            ['name' => 'Discharge of Charge'],
            ['name' => 'Valuation Report'],
            ['name' => 'Survey Plan'],
        ];
        foreach ($document_types as $type_data) {
            $this->db->where('name', $type_data['name']);
            $query = $this->db->get('conveyancing_document_type');
            if ($query->num_rows() == 0) {
                $this->db->insert('conveyancing_document_type', $type_data);
            }
        }

        // Seed conveyancing_process_stages
        // Ensure sequence_order is unique for initial seeding
        $process_stages = [
            ['name' => 'Initiation', 'description' => 'Initial setup and client engagement.', 'sequence_order' => 10],
            ['name' => 'Due Diligence', 'description' => 'Verification of documents and property details.', 'sequence_order' => 20],
            ['name' => 'Documentation', 'description' => 'Drafting and finalization of legal documents.', 'sequence_order' => 30],
            ['name' => 'Stamping & Consents', 'description' => 'Obtaining necessary approvals and payment of duties.', 'sequence_order' => 40],
            ['name' => 'Registration', 'description' => 'Submission of documents for registration at lands office.', 'sequence_order' => 50],
            ['name' => 'Completion', 'description' => 'Final handover and file closure.', 'sequence_order' => 60],
        ];
        foreach ($process_stages as $stage_data) {
            $this->db->where('name', $stage_data['name']);
            $query = $this->db->get('conveyancing_process_stages');
            if ($query->num_rows() == 0) {
                $this->db->insert('conveyancing_process_stages', $stage_data);
            }
        }

        // Seed conveyancing_activity_type
        $activity_types = [
            ['name' => 'Client Meeting', 'description' => 'Meeting with client to discuss case details.'],
            ['name' => 'Document Review', 'description' => 'Review of legal documents for accuracy.'],
            ['name' => 'Correspondence', 'description' => 'Sending or receiving communications related to the case.'],
            ['name' => 'Court Filing', 'description' => 'Submission of documents to court.'],
            ['name' => 'Payment Processing', 'description' => 'Handling financial transactions.'],
        ];
        foreach ($activity_types as $activity_data) {
            $this->db->where('name', $activity_data['name']);
            $query = $this->db->get('conveyancing_activity_type');
            if ($query->num_rows() == 0) {
                $this->db->insert('conveyancing_activity_type', $activity_data);
            }
        }

        // Seed conveyancing_transaction_types
        $transaction_types = [
            ['name' => 'Sale', 'applies_to' => 'Property'],
            ['name' => 'Purchase', 'applies_to' => 'Property'],
            ['name' => 'Lease', 'applies_to' => 'Property'],
            ['name' => 'Charge', 'applies_to' => 'Loan'],
            ['name' => 'Discharge', 'applies_to' => 'Loan'],
        ];
        foreach ($transaction_types as $trans_data) {
            $this->db->where('name', $trans_data['name']);
            $this->db->where('applies_to', $trans_data['applies_to']);
            $query = $this->db->get('conveyancing_transaction_types');
            if ($query->num_rows() == 0) {
                $this->db->insert('conveyancing_transaction_types', $trans_data);
            }
        }

        // Seed conveyancing_instrument_types
        $instrument_types = [
            ['name' => 'Title Deed', 'applies_to' => 'Property'],
            ['name' => 'Sale Agreement', 'applies_to' => 'Contract'],
            ['name' => 'Charge', 'applies_to' => 'Loan'],
            ['name' => 'Discharge', 'applies_to' => 'Loan'],
            ['name' => 'Mutation Form', 'applies_to' => 'Property'],
        ];
        foreach ($instrument_types as $instr_data) {
            $this->db->where('name', $instr_data['name']);
            $this->db->where('applies_to', $instr_data['applies_to']);
            $query = $this->db->get('conveyancing_instrument_types');
            if ($query->num_rows() == 0) {
                $this->db->insert('conveyancing_instrument_types', $instr_data);
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Remove seeded data (optional, can be commented out if you prefer to keep seeded data)
        $this->db->empty_table('conveyancing_instrument_types');
        $this->db->empty_table('conveyancing_transaction_types');
        $this->db->empty_table('conveyancing_activity_type');
        $this->db->empty_table('conveyancing_process_stages');
        $this->db->empty_table('conveyancing_document_type');
        $this->db->empty_table('conveyancing_document_status');

        $this->db->trans_complete(); // Complete the transaction
    }
}
