<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Clients_View extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
        -- Drop the view if it already exists to allow recreation
        IF OBJECT_ID('[dbo].[clients_view]', 'V') IS NOT NULL
            DROP VIEW [dbo].[clients_view];
        GO

        CREATE VIEW [dbo].[clients_view] AS
        SELECT
            clients.id AS id,
            clients.term_id AS term_id,
            clients.discount_percentage AS discount_percentage,
            CASE WHEN clients.company_id IS NULL THEN con.tax_number ELSE com.registrationTaxNb END AS tax_number,
            CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
            CASE WHEN clients.company_id IS NULL THEN isnull(con.foreignFirstName, '') + ' ' + isnull(con.foreignLastName, '') ELSE com.foreignName END AS foreignName,
            CASE WHEN clients.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
            CASE WHEN clients.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
            CASE WHEN clients.company_id IS NULL THEN ( '' + CAST(clients.contact_id AS NVARCHAR(MAX)) ) ELSE ( '' + CAST(clients.company_id AS NVARCHAR(MAX)) ) END AS member_id, -- Cast to NVARCHAR
            'clients' AS model,
            clients.createdBy,
            clients.createdOn,
            clients.modifiedBy,
            clients.modifiedOn,
            ( created.firstName + ' ' + created.lastName) AS createdByName,
            ( modified.firstName + ' ' + modified.lastName) AS modifiedByName,
            NULL AS isThirdParty,
            -- NEW: Email column for clients
            CASE WHEN clients.company_id IS NOT NULL THEN ca.email ELSE ce.email END AS email
        FROM
            clients
        LEFT JOIN companies com ON com.id = clients.company_id
        LEFT JOIN contacts con ON con.id = clients.contact_id
        OUTER APPLY (SELECT TOP 1 email FROM company_addresses WHERE company = com.id ORDER BY email ASC) AS ca -- Join for company email
        OUTER APPLY (SELECT TOP 1 email FROM contact_emails WHERE contact_id = con.id ORDER BY email ASC) AS ce -- Join for contact email
        LEFT JOIN user_profiles created ON created.user_id = clients.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = clients.modifiedBy

        UNION ALL

        SELECT
            vendors.id AS id,
            NULL AS term_id,
            0 AS discount_percentage,
            CASE WHEN vendors.company_id IS NULL THEN con.tax_number ELSE com.registrationTaxNb END AS tax_number,
            CASE WHEN vendors.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
            CASE WHEN vendors.company_id IS NULL THEN con.foreignFirstName+' '+con.foreignLastName ELSE com.foreignName END AS foreignName,
            CASE WHEN vendors.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
            CASE WHEN vendors.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
            CASE WHEN vendors.company_id IS NULL THEN ( '' + CAST(vendors.contact_id AS NVARCHAR(MAX)) ) ELSE ( '' + CAST(vendors.company_id AS NVARCHAR(MAX)) ) END AS member_id, -- Cast to NVARCHAR
            'suppliers' AS model,
            vendors.createdBy,
            vendors.createdOn,
            vendors.modifiedBy,
            vendors.modifiedOn,
            ( created.firstName + ' ' + created.lastName) AS createdByName,
            ( modified.firstName + ' ' + modified.lastName) AS modifiedByName,
            NULL AS isThirdParty,
            -- NEW: Email column for vendors
            CASE WHEN vendors.company_id IS NOT NULL THEN ca.email ELSE ce.email END AS email
        FROM
            vendors
        LEFT JOIN companies com ON com.id = vendors.company_id
        LEFT JOIN contacts con ON con.id = vendors.contact_id
        OUTER APPLY (SELECT TOP 1 email FROM company_addresses WHERE company = com.id ORDER BY email ASC) AS ca -- Join for company email
        OUTER APPLY (SELECT TOP 1 email FROM contact_emails WHERE contact_id = con.id ORDER BY email ASC) AS ce -- Join for contact email
        LEFT JOIN user_profiles created ON created.user_id = vendors.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = vendors.modifiedBy

        UNION ALL

        SELECT
            partners.id AS id,
            NULL AS term_id,
            0 AS discount_percentage,
            NULL AS tax_number, -- Original view had NULL here for partners
            CASE WHEN partners.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS name,
            CASE WHEN partners.company_id IS NULL THEN con.foreignFirstName+' '+con.foreignLastName ELSE com.foreignName END AS foreignName,
            CASE WHEN partners.company_id IS NULL THEN (CASE WHEN con.father!='' THEN con.firstName + ' '+ con.father + ' ' + con.lastName ELSE con.firstName+' '+con.lastName END) ELSE com.name END AS member_name,
            CASE WHEN partners.company_id IS NULL THEN 'Person' ELSE 'Company' END AS type,
            CASE WHEN partners.company_id IS NULL THEN ( '' + CAST(partners.contact_id AS NVARCHAR(MAX)) ) ELSE ( '' + CAST(partners.company_id AS NVARCHAR(MAX)) ) END AS member_id, -- Cast to NVARCHAR
            'partners' AS model,
            partners.createdBy,
            partners.createdOn,
            partners.modifiedBy,
            partners.modifiedOn,
            ( created.firstName + ' ' + created.lastName) AS createdByName,
            ( modified.firstName + ' ' + modified.lastName) AS modifiedByName,
            partners.isThirdParty AS isThirdParty,
            -- NEW: Email column for partners
            CASE WHEN partners.company_id IS NOT NULL THEN ca.email ELSE ce.email END AS email
        FROM
            partners
        LEFT JOIN companies com ON com.id = partners.company_id
        LEFT JOIN contacts con ON con.id = partners.contact_id
        OUTER APPLY (SELECT TOP 1 email FROM company_addresses WHERE company = com.id ORDER BY email ASC) AS ca -- Join for company email
        OUTER APPLY (SELECT TOP 1 email FROM contact_emails WHERE contact_id = con.id ORDER BY email ASC) AS ce -- Join for contact email
        LEFT JOIN user_profiles created ON created.user_id = partners.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = partners.modifiedBy;
        ";

        // Split the SQL by 'GO' and execute each batch
        $statements = array_filter(array_map('trim', explode('GO', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $this->db->query($statement);
            }
        }

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Migration for clients_view failed and transaction was rolled back.');
        } else {
            log_message('info', 'Migration for clients_view completed successfully.');
        }
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
        IF OBJECT_ID('[dbo].[clients_view]', 'V') IS NOT NULL
            DROP VIEW [dbo].[clients_view];
        ";
        $this->db->query($sql);

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Rollback for clients_view failed and transaction was rolled back.');
        } else {
            log_message('info', 'Rollback for clients_view completed successfully.');
        }
    }
}
