-- Create exhibit_document_types table
CREATE TABLE exhibit_document_types (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,

);

-- Create exhibit_document_statuses table
CREATE TABLE exhibit_document_statuses (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
   
);

-- Insert document types
INSERT INTO exhibit_document_types (name) VALUES
('Artifact Photographs'),
('Artifact Descriptions'),
('Provenance Records'),
('Conservation Reports'),
('Condition Reports'),
('Electronic Equipment'),
('Audio/Visual Equipment'),
('Lighting Equipment'),
('Security Equipment'),
('Climate Control Devices'),
('Computers'),
('Laptops'),
('Tablets'),
('Servers'),
('Network Devices'),
('Digital Media'),
('Audio Recordings'),
('Video Recordings'),
('Photographic Negatives'),
('Microfilm'),
('Loan Agreements'),
('Insurance Documents'),
('Shipping Manifests'),
('Customs Documentation'),
('Research Notes'),
('Visitor Feedback'),
('Maintenance Logs'),
('Incident Reports'),
('Miscellaneous'),
('Radio Equipment'),
('Sensors'),
('Interactive Displays'),
('Exhibition Labels'),
('Donor Agreements');

-- Insert document statuses
INSERT INTO exhibit_document_statuses (name) VALUES
('Draft'),
('In Progress'),
('Submitted for Review'),
('Under Review'),
('Pending Approval'),
('Approved'),
('Published'),
('Certified'),
('Rejected'),
('Needs Revision'),
('Returned for Completion'),
('Archived'),
('Superseded'),
('Deaccessioned'),
('Restricted Access'),
('Expired'),
('Renewed'),
('On Loan'),
('In Conservation'),
('On Display'),
('In Storage'),
('Excellent Condition'),
('Good Condition'),
('Fair Condition'),
('Poor Condition'),
('Needs Repair');