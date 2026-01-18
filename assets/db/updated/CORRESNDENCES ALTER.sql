ALTER TABLE correspondences
ADD 
    date_dispatched DATE NULL,
    due_date DATE NULL,
    mode_of_receipt NVARCHAR(50) NULL;

	go
	alter table opinion_comments add added_from_channel nvarchar(5) null

	go
	alter table legal_cases add litigation_case_court_activity_purpose NVARCHAR(255) NULL;
