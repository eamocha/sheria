SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- Drop the view if it exists
IF EXISTS (SELECT * FROM sys.views WHERE name = 'opinions_detailed_view' AND schema_id = SCHEMA_ID('dbo'))
BEGIN
    DROP VIEW [dbo].opinions_detailed_view;
END
GO

-- Recreate the view
CREATE VIEW opinions_detailed_view AS
SELECT TOP (9223372036854775800)
    opinions.id,
    ('LO' + CAST(opinions.id AS nvarchar)) AS opinionId,
    opinions.title,
    CASE
        WHEN opinions.legal_case_id IS NULL THEN ''
        ELSE ('M' + CAST(opinions.legal_case_id AS nvarchar))
    END AS caseId,
    opinions.legal_case_id,
    opinions.user_id,
    opinions.due_date,
    opinions.assigned_to AS assignedToId,
    opinions.reporter AS reportedById,
    opinions.private,
    opinions.priority,
    opinions.opinion_location_id AS opinion_location_id,
    opinion_locations.name AS location,
    opinions.detailed_info AS opinionFulldetailed_info,
    opinions.background_info,
    opinions.legal_question,
    opinions.opinion_status_id,
    opinions.opinion_type_id,
    opinions.estimated_effort,
	 opinions.channel,
	  opinions.requester,
	  opinions.is_visible_to_cp,
    CAST(opinions.createdOn AS DATE) AS createdOn,
    CAST(opinions.modifiedOn AS DATE) AS modifiedOn,
    opinions.modifiedBy,
    opinions.archived,
    tee.effectiveEffort,
    (assigned.firstName + ' ' + assigned.lastName) AS assigned_to,
    (reporter.firstName + ' ' + reporter.lastName) AS reporter,
    (created.firstName + ' ' + created.lastName) AS createdBy,
    (modified.firstName + ' ' + modified.lastName) AS modifiedByName,
    opinions.createdBy AS createdById,
    ts.name AS opinionStatus,
    opinions.archived AS archivedOpinions,
    SUBSTRING(opinions.detailed_info, 1, 50) AS detailed_info,
    SUBSTRING(lg.subject, 1, 50) AS caseSubject,
    lg.subject AS caseFullSubject,
    lg.category AS caseCategory,
    opinions.contract_id,
    contract.name AS contract_name,
    assigned.status AS assignee_status,
    reporter.status AS reporter_status,
    created.status AS creator_status,
    modified.status AS modifier_status,
    opinions.stage,
    opinions.category AS opinionCategory,
    contributors = STUFF(
        (
            SELECT ', ' + (contr.firstName + ' ' + contr.lastName)
            FROM user_profiles AS contr
            INNER JOIN opinion_contributors ON opinions.id = opinion_contributors.opinion_id AND contr.user_id = opinion_contributors.user_id
            FOR XML PATH('')
        ),
        1,
        1,
        ''
    )
FROM opinions
LEFT JOIN user_profiles assigned ON assigned.user_id = opinions.assigned_to
LEFT JOIN user_profiles reporter ON reporter.user_id = opinions.reporter
LEFT JOIN user_profiles created ON created.user_id = opinions.createdBy
LEFT JOIN user_profiles modified ON modified.user_id = opinions.modifiedBy
LEFT JOIN opinion_statuses ts ON ts.id = opinions.opinion_status_id
LEFT JOIN legal_cases AS lg ON lg.id = opinions.legal_case_id
LEFT JOIN contract ON contract.id = opinions.contract_id
LEFT JOIN opinion_effective_effort AS tee ON tee.opinion_id = opinions.id
LEFT JOIN opinion_locations ON opinion_locations.id = opinions.opinion_location_id
WHERE
    opinions.legal_case_id IS NULL OR lg.isDeleted = 0;
GO