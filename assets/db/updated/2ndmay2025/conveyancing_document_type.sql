--DROP TABLE IF EXISTS conveyancing_document_type;
-- Create conveyancing_document_type table
CREATE TABLE conveyancing_document_type (
    id INT PRIMARY KEY IDENTITY(1,1),
    name VARCHAR(255) NOT NULL,
    addedOn DATETIME NOT NULL
);

INSERT INTO conveyancing_document_type (name, addedOn)
VALUES
    ('Valuation report', GETDATE()),
    ('Screenshot of ERP system approvals', GETDATE()),
    ('Sale agreement', GETDATE()),
    ('Original title document', GETDATE()),
    ('Staff ID', GETDATE()),
    ('Staff KRA PIN certificate', GETDATE()),
    ('Spouse ID ', GETDATE()),
    ('Passport ', GETDATE()),
    ('Duly registered Charge Document', GETDATE()),
    ('Original Title with charge entry', GETDATE()),
    ('Stamp Duty receipt/certificate', GETDATE()),
    ('Search or due diligence document', GETDATE()),
    ('Receipt of payment of registration fees', GETDATE()),
    ('Consent by Lands Control Board ', GETDATE()),
    ('Fee Note', GETDATE());
