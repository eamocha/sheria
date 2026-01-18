CREATE TABLE surety_bonds (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,    -- Unique identifier for the surety bond, renamed from bond_id and now BIGINT
    contract_id BIGINT NOT NULL,               -- Foreign Key to link with the contract table
    bond_type NVARCHAR(50) NOT NULL,        -- Type of bond (e.g., 'Performance Bond', 'Bid Bond', 'Payment Bond')
    bond_amount DECIMAL(18, 2) NOT NULL,    -- The monetary value of the bond
    currency_id BIGINT NOT NULL,               -- Foreign Key to link with the iso_currencies table (assuming iso_currencies.id is INT)
    surety_provider NVARCHAR(255) NOT NULL, -- Name of the company providing the bond
    bond_number NVARCHAR(100) UNIQUE NOT NULL, -- Unique bond number issued by the surety provider
    effective_date DATE NOT NULL,           -- Date the bond becomes active
    expiry_date DATE NULL,                  -- Date the bond expires (can be NULL if open-ended or until released)
    released_date DATE NULL,                -- Date the bond was officially released
    bond_status NVARCHAR(50) NOT NULL,      -- Current status of the bond (e.g., 'Active', 'Expired', 'Released', 'Claimed')
    document_id BIGINT NULL,                   -- Optional: Foreign Key to a documents_management_system table for the bond document
    remarks NVARCHAR(MAX) NULL,             -- Any additional notes or comments about the bond
    createdOn DATETIME DEFAULT GETDATE(),   -- Timestamp when the record was created, renamed from created_at
    createdBy BIGINT NOT NULL,           -- User who created the record
    modifiedOn DATETIME DEFAULT GETDATE(),  -- Timestamp when the record was last modified
    modifiedBy BIGINT NULL ,          -- User who last modified the record
	     archived  NVARCHAR(3) DEFAULT 'no' NOT NULL

);

ALTER TABLE surety_bonds
ADD CONSTRAINT FK_SuretyBond_Contract
FOREIGN KEY (contract_id) REFERENCES contract(id);

ALTER TABLE surety_bonds
ADD CONSTRAINT FK_SuretyBond_Currency
FOREIGN KEY (currency_id) REFERENCES iso_currencies(id);

ALTER TABLE surety_bonds
ADD CONSTRAINT FK_SuretyBond_Document
FOREIGN KEY (document_id) REFERENCES documents_management_system(id);
