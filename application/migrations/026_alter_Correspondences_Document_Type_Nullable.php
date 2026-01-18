
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Correspondences_Document_Type_Nullable extends CI_Migration {

    public function up()
    {
        $this->db->trans_start();

        // 1. Check if the foreign key exists and drop it if it does
        $fk_query = $this->db->query("
            SELECT name 
            FROM sys.foreign_keys 
            WHERE parent_object_id = OBJECT_ID('correspondences')
            AND referenced_object_id = OBJECT_ID('correspondence_document_types')
            AND name = 'FK_correspondences_document_type'
        ");

        if ($fk_query->num_rows() > 0) {
            $fk_name = $fk_query->row()->name;
            $this->db->query("ALTER TABLE correspondences DROP CONSTRAINT $fk_name");
        }

        // 2. Check current column type
        $column_check = $this->db->query("
            SELECT t.name AS data_type
            FROM sys.columns c
            JOIN sys.types t ON c.user_type_id = t.user_type_id
            WHERE c.object_id = OBJECT_ID('correspondences')
            AND c.name = 'document_type_id'
        ");

        // 3. Alter column to BIGINT if not already
        if ($column_check->num_rows() > 0 && $column_check->row()->data_type != 'bigint') {
            $this->db->query("ALTER TABLE correspondences ALTER COLUMN document_type_id BIGINT NULL");
        }

        // 4. Verify referenced column is BIGINT
        $ref_column_check = $this->db->query("
            SELECT t.name AS data_type
            FROM sys.columns c
            JOIN sys.types t ON c.user_type_id = t.user_type_id
            WHERE c.object_id = OBJECT_ID('correspondence_document_types')
            AND c.name = 'id'
        ");

        if ($ref_column_check->num_rows() > 0 && $ref_column_check->row()->data_type != 'bigint') {
            throw new Exception("Referenced column correspondence_document_types.id must be BIGINT");
        }

        // 5. Recreate the foreign key constraint
        $this->db->query("
            ALTER TABLE correspondences 
            ADD CONSTRAINT FK_correspondences_document_type 
            FOREIGN KEY (document_type_id) 
            REFERENCES correspondence_document_types(id)
        ");

        $this->db->trans_complete();
    }

    public function down()
    {
        // Typically you wouldn't revert the BIGINT change in production
        // But here's how you could revert to INT if needed
        $this->db->trans_start();

        // 1. Drop the foreign key constraint
        $fk_query = $this->db->query("
            SELECT name 
            FROM sys.foreign_keys 
            WHERE parent_object_id = OBJECT_ID('correspondences')
            AND referenced_object_id = OBJECT_ID('correspondence_document_types')
            AND name = 'FK_correspondences_document_type'
        ");

        if ($fk_query->num_rows() > 0) {
            $fk_name = $fk_query->row()->name;
            $this->db->query("ALTER TABLE correspondences DROP CONSTRAINT $fk_name");
        }

        // 2. Change column back to INT (only if you're sure this won't cause data loss)
        $this->db->query("ALTER TABLE correspondences ALTER COLUMN document_type_id INT NULL");

        // 3. Recreate foreign key with INT type
        $this->db->query("
            ALTER TABLE correspondences 
            ADD CONSTRAINT FK_correspondences_document_type 
            FOREIGN KEY (document_type_id) 
            REFERENCES correspondence_document_types(id)
        ");

        $this->db->trans_complete();
    }
}