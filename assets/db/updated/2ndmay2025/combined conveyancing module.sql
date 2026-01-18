-- Drop tables if they exist
DROP TABLE IF EXISTS conveyancing_instruments;
DROP TABLE IF EXISTS conveyancing_activity;
DROP TABLE IF EXISTS conveyancing_activity_type;
DROP TABLE IF EXISTS conveyancing_document_status;
--DROP TABLE IF EXISTS users; -- Assuming a users table exists
DROP TABLE IF EXISTS conveyancing_document;


-- Create conveyancing_instruments table
CREATE TABLE conveyancing_instruments (
    id BIGINT PRIMARY KEY IDENTITY(1,1),
    title NVARCHAR(255) NOT NULL,
    instrument_type VARCHAR(255) NOT NULL,
    parties VARCHAR(255) NOT NULL,
    initiated_by VARCHAR(255),
    staff_pf_no VARCHAR(255),
    date_initiated DATE NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE(),
    contact_id BIGINT,
    external_counsel VARCHAR(255),
    property_value DECIMAL(18, 2),
    amount_requested DECIMAL(18, 2),
    amount_approved DECIMAL(18, 2),
    createdOn DATETIME,
    createdBy BIGINT,
    modifiedOn DATETIME,
    modifiedBy BIGINT,
    archived VARCHAR(255),
    status VARCHAR(255),
    reference_number VARCHAR(255),
    transaction_type VARCHAR(255),
    assignee_id BIGINT,
    requester_id BIGINT,
    currency_id INT
);

-- Create conveyancing_activity_type table
CREATE TABLE conveyancing_activity_type (
    id BIGINT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(255) NOT NULL,
    description NVARCHAR(MAX) NULL,
    createdOn DATETIME,
    createdBy BIGINT,
    modifiedOn DATETIME,
    modifiedBy BIGINT
);

-- Create conveyancing_activity table
CREATE TABLE conveyancing_activity (
    id BIGINT PRIMARY KEY IDENTITY(1,1),
    conveyancing_instrument_id BIGINT,
    activity_type BIGINT,
    activity_details TEXT NULL,
    performed_by BIGINT,
    activity_date BIGINT NULL,
    remarks NVARCHAR(250) NULL,
    activity_status NVARCHAR(50),
    actor_display_text NVARCHAR(255),
    actor_call_toaction_buttontext NVARCHAR(255),
    modifiedOn DATETIME,
    modifiedBy BIGINT,
    FOREIGN KEY (conveyancing_instrument_id) REFERENCES conveyancing_instruments(id),
    FOREIGN KEY (activity_type) REFERENCES conveyancing_activity_type(id),
    FOREIGN KEY (performed_by) REFERENCES users(id),
    FOREIGN KEY (modifiedBy) REFERENCES users(id)
);


-- Create conveyancing_document_status table
CREATE TABLE conveyancing_document_status (
    id INT PRIMARY KEY IDENTITY(1,1),
    name VARCHAR(255),
    addedon DATETIME
);

---- Create conveyancing_document table
----CREATE TABLE conveyancing_document (
----    id INT PRIMARY KEY IDENTITY(1,1),
----    conveyancing_id INT,
----    file_name VARCHAR(255) NOT NULL,
----    file_path VARCHAR(255) NOT NULL,
----    created_at DATETIME DEFAULT GETDATE(),
----    updated_at DATETIME DEFAULT GETDATE(),
----    FOREIGN KEY (conveyancing_id) REFERENCES conveyancing_instruments(id) ON DELETE CASCADE
----);

-- Create conveyancing_document_type table
CREATE TABLE conveyancing_document_type (
    id INT PRIMARY KEY IDENTITY(1,1),
    name VARCHAR(255) NOT NULL,
    addedOn DATETIME NOT NULL
);


-- Add foreign key constraints (if users table exists)
ALTER TABLE conveyancing_instruments
ADD FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (modifiedBy) REFERENCES users(id),
    FOREIGN KEY (assignee_id) REFERENCES users(id),
    FOREIGN KEY (requester_id) REFERENCES users(id);

ALTER TABLE conveyancing_activity_type
ADD FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (modifiedBy) REFERENCES users(id);

