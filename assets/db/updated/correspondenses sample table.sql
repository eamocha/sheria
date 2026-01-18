-- Sample Data for correspondence_types
INSERT INTO correspondence_types (name, createdOn, createdBy) VALUES
('Incoming Letter', GETDATE(), 1),
('Outgoing Letter', GETDATE(), 1),
('Email', GETDATE(), 2),
('Memo', GETDATE(), 2);

-- Sample Data for correspondence_statuses
INSERT INTO correspondence_statuses (name, createdOn, createdBy) VALUES
('Received', GETDATE(), 1),
('In Progress', GETDATE(), 2),
('Completed', GETDATE(), 1),
('Pending', GETDATE(), 2),
('Approved', GETDATE(),1);

-- Sample Data for correspondence_workflow_processes
INSERT INTO correspondence_workflow_processes (name, correspondence_type_id, steps, createdOn, createdBy) VALUES
('Incoming Document Processing', 1, '["Received", "Assigned", "Reviewed", "Approved", "Filed"]', GETDATE(), 1),
('Outgoing Document Processing', 2, '["Drafted", "Reviewed", "Approved", "Sent", "Filed"]', GETDATE(), 2),
('Email Processing', 3, '["Received", "Acknowledged", "Actioned", "Closed"]', GETDATE(), 1),
('Memo Processing', 4, '["Drafted", "Reviewed", "Approved", "Distributed"]', GETDATE(), 2);

-- Sample Data for correspondences
INSERT INTO correspondences (correspondence_type_id, sender, recipient, subject, body, date_received, document_date, serial_number, status_id, requires_signature, assigned_to, filename, comments, createdOn, createdBy, document_id, document_type_id) VALUES
(1, 3, 4, 'Request for Information', 'Please provide the following information...', GETDATE(), '2025-01-10', 'IC-2025-001', 1, 0, 5, 'request_info.pdf', 'Urgent', GETDATE(), 1, 1, 1),
(2, 4, 3, 'Response to Request', 'Here is the information you requested.', GETDATE(), '2025-01-15', 'OC-2025-002', 2, 1, 6, 'response.docx', 'For your review', GETDATE(), 2, 2, 2),
(3, 5, 7, 'Meeting Invitation', 'You are invited to a meeting on...', GETDATE(), '2025-01-20', 'EM-2025-003', 3, 0, 5, 'invitation.pdf', 'Please RSVP', GETDATE(), 1, 3, 3),
(4, 7, 5, 'Internal Memo', 'Regarding the upcoming policy change...', GETDATE(), '2025-02-01', 'MM-2025-004', 4, 0, 6, 'memo.docx', 'For internal distribution', GETDATE(), 2, 4, 4),
(1, 8, 9, 'Application for Leave', 'I am writing to apply for leave from...', GETDATE(), '2025-02-05', 'IC-2025-005', 1, 1, 7, 'leave_application.pdf', 'Annual leave', GETDATE(), 1, 5, 1);

-- Sample Data for correspondence_workflow
INSERT INTO correspondence_workflow (correspondence_id, workflow_process_id, status, createdOn, createdBy) VALUES
(1, 1, 'Received', GETDATE(), 1),
(1, 1, 'Assigned', GETDATE(), 5),
(1, 1, 'Reviewed', GETDATE(), 6),
(2, 2, 'Drafted', GETDATE(), 4),
(2, 2, 'Reviewed', GETDATE(), 3),
(3, 3, 'Received', GETDATE(), 5),
(3, 3, 'Acknowledged', GETDATE(), 7),
(4, 4, 'Drafted', GETDATE(), 5),
(4, 4, 'Reviewed', GETDATE(), 7),
(5, 1, 'Received', GETDATE(), 8);

-- Sample Data for correspondence_activity_log
INSERT INTO correspondence_activity_log (correspondence_id, user_id, action, details, createdOn, createdBy) VALUES
(1, 5, 'Received', 'Correspondence received by Front Office', GETDATE(), 1),
(1, 5, 'Assigned', 'Correspondence assigned to John Doe', GETDATE(), 5),
(1, 6, 'Reviewed', 'Correspondence reviewed by Jane Smith', GETDATE(), 6),
(2, 4, 'Drafted', 'Correspondence drafted by Peter Jones', GETDATE(), 4),
(2, 3, 'Reviewed', 'Correspondence reviewed by Mary Brown', GETDATE(), 3),
(3, 5, 'Received', 'Email received', GETDATE(), 5),
(3, 7, 'Acknowledged', 'Email acknowledged by David Wilson', GETDATE(), 7),
(4, 5, 'Drafted', 'Memo drafted by Peter Jones', GETDATE(), 5),
(4, 7, 'Reviewed', 'Memo reviewed by David Wilson', GETDATE(), 7),
(5, 8, 'Received', 'Application received', GETDATE(), 8);

-- Sample data for correspondence_documents
INSERT INTO correspondence_documents (name, filename, path, size, mime_type, createdOn, createdBy) VALUES
('Request for Information', 'request_info.pdf', '/path/to/request_info.pdf', 1024, 'application/pdf', GETDATE(), 1),
('Response to Request', 'response.docx', '/path/to/response.docx', 2048, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', GETDATE(), 2),
('Meeting Invitation', 'invitation.pdf', '/path/to/invitation.pdf', 768, 'application/pdf', GETDATE(), 1),
('Internal Memo', 'memo.docx', '/path/to/memo.docx', 1536, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', GETDATE(), 2),
('Leave Application', 'leave_application.pdf', '/path/to/leave_application.pdf', 921, 'application/pdf', GETDATE(), 1);

-- Sample data for correspondence_document_types
INSERT INTO correspondence_document_types (name, description, createdOn, createdBy) VALUES
('Letter', 'External correspondence letter', GETDATE(), 1),
('Document', 'General document', GETDATE(), 2),
('Email', 'Electronic mail', GETDATE(), 1),
('Memo', 'Internal memorandum', GETDATE(), 2),
('Application', 'Formal application', GETDATE(), 1);
