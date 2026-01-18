<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Criminal_Case_Tables_And_Seed extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Create case_offense_subcategory table ---
        if (!$this->db->table_exists('case_offense_subcategory')) {
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
                'offense_type_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'is_active' => [
                    'type' => 'BIT',
                    'null' => TRUE,
                    'default' => 1,
                ],
                'CONSTRAINT pk_case_offense_subcategory PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('case_offense_subcategory', TRUE);

            // Add unique constraint using raw SQL
            $this->db->query("
                ALTER TABLE [dbo].[case_offense_subcategory]
                ADD CONSTRAINT [UQ_case_offense_subcategory_name_type] UNIQUE NONCLUSTERED
                (
                    [name] ASC,
                    [offense_type_id] ASC
                );
            ");
        }

        // --- 2. Seed case_types (if not already present) and case_offense_subcategory ---

        // Define main categories to insert into case_types
        $main_categories = [
            ['name' => 'Telecommunication Offences', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Radio Communication/Frequency Spectrum', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Broadcasting Offences', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Postal/Courier Offences', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Electronic Transactions Offences', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Cyber Offences', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Standards and Type Approval', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Consumer Protection Offences', 'criminal' => 'yes', 'isDeleted' => 0],
            ['name' => 'Tariff Regulations Offences', 'criminal' => 'yes', 'isDeleted' => 0],
        ];

        foreach ($main_categories as $category_data) {
            $this->db->where('name', $category_data['name']);
            $query = $this->db->get('case_types');
            if ($query->num_rows() == 0) {
                $this->db->insert('case_types', $category_data);
            }
        }

        // Define subcategories and link them to main categories
        $subcategories_data = [
            'Telecommunication Offences' => [
                'SIM Card Hawking', 'Using numbering or addresses contrary to Regulations',
                'Unlicensed telecommunications systems', 'Obtaining service dishonestly',
                'Improper use of system', 'Modification of messages',
                'Interception and disclosure of information', 'Tampering with telecommunication plant',
                'Severing with intent to steal', 'Trespass and wilful obstruction of telecommunication officer'
            ],
            'Radio Communication/Frequency Spectrum' => [
                'Unlicensed radio communication systems', 'Unlawfully sending of misleading messages',
                'Deliberate interference with radio communication'
            ],
            'Broadcasting Offences' => [
                'Unlicensed broadcasting services', 'Providing broadcasting service which is not of a description specified in the license',
                'Providing broadcasting services in an area not licensed', 'Broadcasting in contravention of the Act or the license conditions'
            ],
            'Postal/Courier Offences' => [
                'Unlicensed postal/courier services', 'Unlawful conveying letter or postal article',
                'Performing any service incidental to conveying letter/postal article', 'Unlawful delivering or tendering letter/postal article',
                'Unlawful collection of letters or postal articles', 'Damaging letter box',
                'Unlawful affixing materials on post office', 'Unlawful opening or delivery of postal articles',
                'Transmitting offensive material by post', 'Use of fictitious stamps',
                'Unlawful use of certain words', 'Transmitting prohibited articles by post',
                'Interfering with postal installation'
            ],
            'Electronic Transactions Offences' => [
                'Unauthorized operation of an electronic certification system'
            ],
            'Cyber Offences' => [
                'Damaging/denying access to a computer system', 'Tampering with computer source documents',
                'Publishing of obscene information in electronic form', 'Publication for fraudulent purpose',
                'Re-programming of mobile telephone', 'Possession or supply of anything for reprogramming mobile telephone'
            ],
            'Standards and Type Approval' => [
                'Selling/offering for sale radio communication apparatus', 'Letting/hiring a radio communication apparatus',
                'Advertisement/dealing in radio communication apparatus'
            ],
            'Consumer Protection Offences' => [
                'Promoting, glamorizing or marketing alcohol and tobacco products or other harmful substances to children',
                'Using automated calling systems without prior consent of the subscriber',
                'Sending electronic mail without a valid address', 'Failing to perform measurement, reporting and record keeping',
                'Failing to reach a target', 'Failing to submit information',
                'Submits or publishes false or misleading information', 'Obstructing or preventing an inspection or investigation',
                'Engaging in any act or omission to defeat the purposes of these Regulations'
            ],
            'Tariff Regulations Offences' => [
                'Contravening tariff regulations'
            ],
        ];

        foreach ($subcategories_data as $parent_name => $subcats) {
            $parent_query = $this->db->get_where('case_types', ['name' => $parent_name]);
            if ($parent_query->num_rows() > 0) {
                $parent_id = $parent_query->row()->id;
                foreach ($subcats as $subcat_name) {
                    $this->db->where('name', $subcat_name);
                    $this->db->where('offense_type_id', $parent_id);
                    $query = $this->db->get('case_offense_subcategory');
                    if ($query->num_rows() == 0) {
                        $this->db->insert('case_offense_subcategory', [
                            'name' => $subcat_name,
                            'offense_type_id' => $parent_id,
                            'is_active' => 1
                        ]);
                    }
                }
            }
        }

        // --- 3. Create criminal_case_details table ---
        if (!$this->db->table_exists('criminal_case_details')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'case_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'origin_of_case' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ],
                'offence_subcategory_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'status_of_case' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => FALSE,
                ],
                'initial_entry_document_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'authorization_document_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'date_investigation_authorized' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'police_station_reported' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'police_station_ob_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ],
                'police_case_file_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '30',
                    'null' => TRUE,
                ],
                'CONSTRAINT pk_criminal_case_details PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('criminal_case_details', TRUE);
        }

        // --- 4. Create suspect_arrest table ---
        if (!$this->db->table_exists('suspect_arrest')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'case_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'arrest_date' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'arrested_contact_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'arrested_gender' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'arrested_age' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'arrest_police_station' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ],
                'arrest_ob_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'arrest_case_file_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'arrest_attachments' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'arrest_remarks' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'createdOn datetime NULL DEFAULT GETDATE()',
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'modifiedOn datetime NULL',
                'bail_status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
                ],
                'arrest_location' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'place_arrested' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'arresting_officers' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'archived' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '2',
                    'null' => TRUE, // Changed from NOT NULL to NULL as per default 'no' implies it can be null before default is applied
                    'default' => 'no',
                ],
                'CONSTRAINT pk_suspect_arrest PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('suspect_arrest', TRUE);
        }

        // --- 5. Add Foreign Key Constraints ---

        // FK_case_offense_subcategory_case_types
        $fk_name_subcat = 'FK_case_offense_subcategory_case_types';
        $check_fk_sql_subcat = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_subcat}' AND parent_object_id = OBJECT_ID('[dbo].[case_offense_subcategory]');";
        $query_result_subcat = $this->db->query($check_fk_sql_subcat);
        $fk_exists_subcat = false;
        if ($query_result_subcat && $query_result_subcat->num_rows() > 0 && isset($query_result_subcat->row()->count) && $query_result_subcat->row()->count > 0) {
            $fk_exists_subcat = true;
        }

        if (!$fk_exists_subcat) {
            $this->db->query("
                ALTER TABLE [dbo].[case_offense_subcategory] WITH CHECK ADD CONSTRAINT [{$fk_name_subcat}] FOREIGN KEY([offense_type_id])
                REFERENCES [dbo].[case_types] ([id])
                ON UPDATE CASCADE
                ON DELETE CASCADE;
            ");
            $this->db->query("ALTER TABLE [dbo].[case_offense_subcategory] CHECK CONSTRAINT [{$fk_name_subcat}];");
        }

        // FK_criminal_case_details_legal_cases
        $fk_name_criminal_case = 'FK_criminal_case_details_legal_cases';
        $check_fk_sql_criminal_case = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_criminal_case}' AND parent_object_id = OBJECT_ID('[dbo].[criminal_case_details]');";
        $query_result_criminal_case = $this->db->query($check_fk_sql_criminal_case);
        $fk_exists_criminal_case = false;
        if ($query_result_criminal_case && $query_result_criminal_case->num_rows() > 0 && isset($query_result_criminal_case->row()->count) && $query_result_criminal_case->row()->count > 0) {
            $fk_exists_criminal_case = true;
        }

        if (!$fk_exists_criminal_case) {
            $this->db->query("
                ALTER TABLE [dbo].[criminal_case_details] WITH CHECK ADD CONSTRAINT [{$fk_name_criminal_case}] FOREIGN KEY([case_id])
                REFERENCES [dbo].[legal_cases] ([id])
                ON DELETE CASCADE;
            ");
            $this->db->query("ALTER TABLE [dbo].[criminal_case_details] CHECK CONSTRAINT [{$fk_name_criminal_case}];");
        }

        // FK_suspect_arrest_legal_cases
        $fk_name_suspect_arrest_case = 'FK_suspect_arrest_legal_cases';
        $check_fk_sql_suspect_arrest_case = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_suspect_arrest_case}' AND parent_object_id = OBJECT_ID('[dbo].[suspect_arrest]');";
        $query_result_suspect_arrest_case = $this->db->query($check_fk_sql_suspect_arrest_case);
        $fk_exists_suspect_arrest_case = false;
        if ($query_result_suspect_arrest_case && $query_result_suspect_arrest_case->num_rows() > 0 && isset($query_result_suspect_arrest_case->row()->count) && $query_result_suspect_arrest_case->row()->count > 0) {
            $fk_exists_suspect_arrest_case = true;
        }

        if (!$fk_exists_suspect_arrest_case) {
            $this->db->query("
                ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [{$fk_name_suspect_arrest_case}] FOREIGN KEY([case_id])
                REFERENCES [dbo].[legal_cases] ([id])
                ON DELETE CASCADE;
            ");
            $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [{$fk_name_suspect_arrest_case}];");
        }

        // FK_suspect_arrest_contacts (for arrested_contact_id)
        $fk_name_suspect_arrest_contact = 'FK_suspect_arrest_contacts';
        $check_fk_sql_suspect_arrest_contact = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_suspect_arrest_contact}' AND parent_object_id = OBJECT_ID('[dbo].[suspect_arrest]');";
        $query_result_suspect_arrest_contact = $this->db->query($check_fk_sql_suspect_arrest_contact);
        $fk_exists_suspect_arrest_contact = false;
        if ($query_result_suspect_arrest_contact && $query_result_suspect_arrest_contact->num_rows() > 0 && isset($query_result_suspect_arrest_contact->row()->count) && $query_result_suspect_arrest_contact->row()->count > 0) {
            $fk_exists_suspect_arrest_contact = true;
        }

        if (!$fk_exists_suspect_arrest_contact) {
            $this->db->query("
                ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [{$fk_name_suspect_arrest_contact}] FOREIGN KEY([arrested_contact_id])
                REFERENCES [dbo].[contacts] ([id])
                ON DELETE CASCADE;
            ");
            $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [{$fk_name_suspect_arrest_contact}];");
        }

        // FK_suspect_arrest_attachments (for arrest_attachments) - Assuming documents_management_system table
        $fk_name_suspect_arrest_attachments = 'FK_suspect_arrest_attachments';
        $check_fk_sql_suspect_arrest_attachments = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_suspect_arrest_attachments}' AND parent_object_id = OBJECT_ID('[dbo].[suspect_arrest]');";
        $query_result_suspect_arrest_attachments = $this->db->query($check_fk_sql_suspect_arrest_attachments);
        $fk_exists_suspect_arrest_attachments = false;
        if ($query_result_suspect_arrest_attachments && $query_result_suspect_arrest_attachments->num_rows() > 0 && isset($query_result_suspect_arrest_attachments->row()->count) && $query_result_suspect_arrest_attachments->row()->count > 0) {
            $fk_exists_suspect_arrest_attachments = true;
        }

        if (!$fk_exists_suspect_arrest_attachments && $this->db->table_exists('documents_management_system')) {
            $this->db->query("
                ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [{$fk_name_suspect_arrest_attachments}] FOREIGN KEY([arrest_attachments])
                REFERENCES [dbo].[documents_management_system] ([id])
                ON DELETE SET NULL;
            ");
            $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [{$fk_name_suspect_arrest_attachments}];");
        }

        // FK_suspect_arrest_createdBy (for createdBy)
        $fk_name_suspect_arrest_createdBy = 'FK_suspect_arrest_createdBy';
        $check_fk_sql_suspect_arrest_createdBy = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_suspect_arrest_createdBy}' AND parent_object_id = OBJECT_ID('[dbo].[suspect_arrest]');";
        $query_result_suspect_arrest_createdBy = $this->db->query($check_fk_sql_suspect_arrest_createdBy);
        $fk_exists_suspect_arrest_createdBy = false;
        if ($query_result_suspect_arrest_createdBy && $query_result_suspect_arrest_createdBy->num_rows() > 0 && isset($query_result_suspect_arrest_createdBy->row()->count) && $query_result_suspect_arrest_createdBy->row()->count > 0) {
            $fk_exists_suspect_arrest_createdBy = true;
        }

        if (!$fk_exists_suspect_arrest_createdBy) {
            $this->db->query("
                ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [{$fk_name_suspect_arrest_createdBy}] FOREIGN KEY([createdBy])
                REFERENCES [dbo].[users] ([id])
                ON DELETE NO ACTION ON UPDATE NO ACTION;
            ");
            $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [{$fk_name_suspect_arrest_createdBy}];");
        }

        // FK_suspect_arrest_modifiedBy (for modifiedBy)
        $fk_name_suspect_arrest_modifiedBy = 'FK_suspect_arrest_modifiedBy';
        $check_fk_sql_suspect_arrest_modifiedBy = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name_suspect_arrest_modifiedBy}' AND parent_object_id = OBJECT_ID('[dbo].[suspect_arrest]');";
        $query_result_suspect_arrest_modifiedBy = $this->db->query($check_fk_sql_suspect_arrest_modifiedBy);
        $fk_exists_suspect_arrest_modifiedBy = false;
        if ($query_result_suspect_arrest_modifiedBy && $query_result_suspect_arrest_modifiedBy->num_rows() > 0 && isset($query_result_suspect_arrest_modifiedBy->row()->count) && $query_result_suspect_arrest_modifiedBy->row()->count > 0) {
            $fk_exists_suspect_arrest_modifiedBy = true;
        }

        if (!$fk_exists_suspect_arrest_modifiedBy) {
            $this->db->query("
                ALTER TABLE [dbo].[suspect_arrest] WITH CHECK ADD CONSTRAINT [{$fk_name_suspect_arrest_modifiedBy}] FOREIGN KEY([modifiedBy])
                REFERENCES [dbo].[users] ([id])
                ON DELETE NO ACTION ON UPDATE NO ACTION;
            ");
            $this->db->query("ALTER TABLE [dbo].[suspect_arrest] CHECK CONSTRAINT [{$fk_name_suspect_arrest_modifiedBy}];");
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Foreign Key Constraints First (in reverse order of dependency) ---
        $fk_names_to_drop = [
            'FK_suspect_arrest_modifiedBy',
            'FK_suspect_arrest_createdBy',
            'FK_suspect_arrest_attachments',
            'FK_suspect_arrest_contacts',
            'FK_suspect_arrest_legal_cases',
            'FK_criminal_case_details_legal_cases',
            'FK_case_offense_subcategory_case_types',
        ];
        foreach ($fk_names_to_drop as $fk_name) {
            // Determine the parent table based on the FK name prefix
            $parent_table = '';
            if (strpos($fk_name, 'FK_suspect_arrest_') === 0) {
                $parent_table = 'suspect_arrest';
            } elseif (strpos($fk_name, 'FK_criminal_case_details_') === 0) {
                $parent_table = 'criminal_case_details';
            } elseif (strpos($fk_name, 'FK_case_offense_subcategory_') === 0) {
                $parent_table = 'case_offense_subcategory';
            }

            if (!empty($parent_table)) {
                $query_result = $this->db->query("SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[" . $parent_table . "]');");
                if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
                    $this->db->query("ALTER TABLE [dbo].[" . $parent_table . "] DROP CONSTRAINT [{$fk_name}];");
                }
            }
        }

        // --- Drop Tables (in reverse order of creation, considering dependencies) ---
        if ($this->db->table_exists('suspect_arrest')) {
            $this->dbforge->drop_table('suspect_arrest', TRUE);
        }
        if ($this->db->table_exists('criminal_case_details')) {
            $this->dbforge->drop_table('criminal_case_details', TRUE);
        }

        // Delete seeded data from case_offense_subcategory
        // This will only delete the specific subcategories added by this script
        $subcategories_to_delete = [
            'SIM Card Hawking', 'Using numbering or addresses contrary to Regulations', 'Unlicensed telecommunications systems',
            'Obtaining service dishonestly', 'Improper use of system', 'Modification of messages',
            'Interception and disclosure of information', 'Tampering with telecommunication plant',
            'Severing with intent to steal', 'Trespass and wilful obstruction of telecommunication officer',
            'Unlicensed radio communication systems', 'Unlawfully sending of misleading messages',
            'Deliberate interference with radio communication', 'Unlicensed broadcasting services',
            'Providing broadcasting service which is not of a description specified in the license',
            'Providing broadcasting services in an area not licensed', 'Broadcasting in contravention of the Act or the license conditions',
            'Postal/Courier Offences', 'Unlawful conveying letter or postal article',
            'Performing any service incidental to conveying letter/postal article', 'Unlawful delivering or tendering letter/postal article',
            'Unlawful collection of letters or postal articles', 'Damaging letter box',
            'Unlawful affixing materials on post office', 'Unlawful opening or delivery of postal articles',
            'Transmitting offensive material by post', 'Use of fictitious stamps',
            'Unlawful use of certain words', 'Transmitting prohibited articles by post',
            'Interfering with postal installation', 'Unauthorized operation of an electronic certification system',
            'Damaging/denying access to a computer system', 'Tampering with computer source documents',
            'Publishing of obscene information in electronic form', 'Publication for fraudulent purpose',
            'Re-programming of mobile telephone', 'Possession or supply of anything for reprogramming mobile telephone',
            'Selling/offering for sale radio communication apparatus', 'Letting/hiring a radio communication apparatus',
            'Advertisement/dealing in radio communication apparatus',
            'Promoting, glamorizing or marketing alcohol and tobacco products or other harmful substances to children',
            'Using automated calling systems without prior consent of the subscriber',
            'Sending electronic mail without a valid address', 'Failing to perform measurement, reporting and record keeping',
            'Failing to reach a target', 'Failing to submit information',
            'Submits or publishes false or misleading information', 'Obstructing or preventing an inspection or investigation',
            'Engaging in any act or omission to defeat the purposes of these Regulations',
            'Contravening tariff regulations'
        ];
        $this->db->where_in('name', $subcategories_to_delete);
        $this->db->delete('case_offense_subcategory');

        // Delete seeded data from case_types
        // This will only delete the specific categories added by this script
        $main_categories_to_delete = [
            'Telecommunication Offences', 'Radio Communication/Frequency Spectrum', 'Broadcasting Offences',
            'Postal/Courier Offences', 'Electronic Transactions Offences', 'Cyber Offences',
            'Standards and Type Approval', 'Consumer Protection Offences', 'Tariff Regulations Offences'
        ];
        $this->db->where_in('name', $main_categories_to_delete);
        $this->db->delete('case_types');


        if ($this->db->table_exists('case_offense_subcategory')) {
            // Drop unique constraint first
            $this->db->query("IF EXISTS (SELECT * FROM sys.indexes WHERE name = 'UQ_case_offense_subcategory_name_type' AND object_id = OBJECT_ID('[dbo].[case_offense_subcategory]')) ALTER TABLE [dbo].[case_offense_subcategory] DROP CONSTRAINT [UQ_case_offense_subcategory_name_type];");
            $this->dbforge->drop_table('case_offense_subcategory', TRUE);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
