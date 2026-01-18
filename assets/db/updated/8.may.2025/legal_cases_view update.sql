SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

DROP VIEW [dbo].[legal_cases_grid];
GO

CREATE VIEW [dbo].[legal_cases_grid] AS 
SELECT TOP(9223372036854775800) 
    legal_cases.id,
    CASE WHEN legal_cases.channel = 'CP' THEN 'yes' ELSE 'no' END AS isCP,
    legal_cases.channel, 
    legal_cases.case_status_id, 
    legal_cases.case_type_id, 
    legal_cases.provider_group_id, 
    legal_cases.user_id,
    legal_cases.contact_id, 
    legal_cases.client_id, 
    legal_cases.subject, 
    legal_cases.description, 
    legal_cases.latest_development, 
    legal_cases.priority, 
    legal_cases.arrivalDate,  
    legal_cases.caseArrivalDate, 
    legal_cases.dueDate, 
    legal_cases.closedOn, 
    legal_cases.statusComments,
    legal_cases.category, 
    legal_cases.caseValue, 
    legal_cases.recoveredValue, 
    legal_cases.judgmentValue, 
    legal_cases.internalReference, 
    legal_cases.externalizeLawyers, 
    legal_cases.estimatedEffort,
    CAST(legal_cases.createdOn as DATE) as createdOn, 
    CAST(legal_cases.modifiedOn as DATE) as modifiedOn, 
    legal_cases.createdBy, 
    legal_cases.modifiedBy,
    legal_cases.archived, 
    legal_cases.private,
    legal_cases.timeTrackingBillable,
    legal_cases.expensesBillable, 
    lcee.effectiveEffort, 
    'M' + CAST(legal_cases.id AS nvarchar) AS caseID, 
    workflow_status.name as status, 
    case_types.name as type,
    provider_groups.name as providerGroup, 
    UP.firstName + ' ' + UP.lastName AS assignee, 
    legal_cases.archived as archivedCases, 
    legal_case_litigation_details.id AS litigation_details_id, 
    legal_case_litigation_details.court_type_id AS court_type_id,
    legal_case_litigation_details.court_degree_id AS court_degree_id, 
    legal_case_litigation_details.court_region_id AS court_region_id, 
    legal_case_litigation_details.court_id AS court_id,
    legal_case_litigation_details.comments AS comments, 
    legal_case_litigation_details.sentenceDate AS sentenceDate,
    com.name AS company, 
    com.id AS company_id, 
    (CASE WHEN conRE.father!='' THEN conRE.firstName + ' '+ conRE.father + ' ' + conRE.lastName ELSE conRE.firstName+' '+conRE.lastName END) AS contact, 
    (CASE WHEN conHE.father!='' THEN conHE.firstName + ' '+ conHE.father + ' ' + conHE.lastName ELSE conHE.firstName+' '+conHE.lastName END) AS contactContributor, 
    (CASE WHEN conExtLaw.father!='' THEN conExtLaw.firstName + ' '+ conExtLaw.father + ' ' + conExtLaw.lastName ELSE conExtLaw.firstName+' '+conExtLaw.lastName END) AS contactOutsourceTo, 
    compiesExtLaw.name AS companyOutsourceTo,
    legal_case_litigation_external_references.number AS litigationExternalRef, 
    clients_view.name AS clientName, 
    clients_view.type AS clientType, 
    legal_cases.referredBy, 
    legal_cases.requestedBy,
    (CASE WHEN referredByContact.father!='' THEN referredByContact.firstName + ' '+ referredByContact.father + ' ' + referredByContact.lastName ELSE referredByContact.firstName+' '+referredByContact.lastName END) AS referredByName, 
    (CASE WHEN requestedByContact.father!='' THEN requestedByContact.firstName + ' '+ requestedByContact.father + ' ' + requestedByContact.lastName ELSE requestedByContact.firstName+' '+requestedByContact.lastName END) AS requestedByName,
    legal_case_containers.subject AS legalCaseContainerSubject, 
    legal_cases.legal_case_stage_id as legal_case_stage_id,
    -- New closure-related columns
    legal_cases.closure_requested_by,
    (CASE WHEN closureRequestedByContact.father!='' THEN closureRequestedByContact.firstName + ' '+ closureRequestedByContact.father + ' ' + closureRequestedByContact.lastName ELSE closureRequestedByContact.firstName+' '+closureRequestedByContact.lastName END) AS closureRequestedByName,
    legal_cases.closed_by,
    (CASE WHEN closedByContact.father!='' THEN closedByContact.firstName + ' '+ closedByContact.father + ' ' + closedByContact.lastName ELSE closedByContact.firstName+' '+closedByContact.lastName END) AS closedByName,
    legal_cases.closure_comments,
    legal_cases.approval_step,
    legal_cases.first_litigation_case_court_activity_purpose,
    legal_cases.closedBy_comments,
    opponentNames = STUFF((SELECT ', ' +
        (CASE WHEN legal_case_opponents.opponent_member_type = 'company'
        THEN opponentCompany.name
        ELSE (CASE WHEN opponentContact.father!='' THEN opponentContact.firstName + ' '+ opponentContact.father + ' ' + opponentContact.lastName ELSE opponentContact.firstName+' '+opponentContact.lastName END) END)
         FROM legal_case_opponents
         INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id
         LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'
         LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'
         WHERE legal_case_opponents.case_id = legal_cases.id
        FOR XML PATH('')), 1, 1, ''),
    legal_cases.legal_case_client_position_id as legal_case_client_position_id,
    legal_cases.legal_case_success_probability_id as legal_case_success_probability_id
FROM legal_cases
LEFT JOIN legal_cases_companies lcccom ON lcccom.case_id = legal_cases.id
LEFT JOIN companies com ON lcccom.company_id = com.id
LEFT JOIN legal_cases_contacts lccre ON lccre.case_id = legal_cases.id AND lccre.contactType = 'contact'
LEFT JOIN contacts conRE ON conRE.id = lccre.contact_id
LEFT JOIN legal_cases_contacts lccch ON lccch.case_id = legal_cases.id AND lccch.contactType = 'contributor'
LEFT JOIN contacts conHE ON conHE.id = lccch.contact_id
LEFT JOIN legal_cases_contacts lccExtLaw ON lccExtLaw.case_id = legal_cases.id AND lccExtLaw.contactType = 'external lawyer'
LEFT JOIN contacts conExtLaw ON conExtLaw.id = lccExtLaw.contact_id
LEFT JOIN legal_cases_companies lccompaniesExtLaw ON lccompaniesExtLaw.case_id = legal_cases.id AND lccompaniesExtLaw.companyType = 'external lawyer'
LEFT JOIN companies compiesExtLaw ON compiesExtLaw.id = lccompaniesExtLaw.company_id
LEFT JOIN workflow_status ON workflow_status.id = legal_cases.case_status_id
INNER JOIN case_types ON case_types.id = legal_cases.case_type_id
INNER JOIN provider_groups ON provider_groups.id = legal_cases.provider_group_id
LEFT JOIN user_profiles as UP ON UP.user_id = legal_cases.user_id
LEFT JOIN legal_case_effective_effort AS lcee ON lcee.legal_case_id = legal_cases.id
LEFT JOIN legal_case_litigation_details ON legal_case_litigation_details.id = legal_cases.stage
LEFT JOIN legal_case_litigation_external_references ON legal_case_litigation_external_references.stage = legal_cases.stage
LEFT JOIN clients_view ON clients_view.id = legal_cases.client_id AND clients_view.model = 'clients'
LEFT JOIN contacts as referredByContact ON referredByContact.id = legal_cases.referredBy
LEFT JOIN contacts as requestedByContact ON requestedByContact.id = legal_cases.requestedBy
LEFT JOIN legal_case_related_containers ON legal_case_related_containers.legal_case_id = legal_cases.id
LEFT JOIN legal_case_containers ON legal_case_containers.id = legal_case_related_containers.legal_case_container_id
-- New joins for the closure contacts
LEFT JOIN contacts as closureRequestedByContact ON closureRequestedByContact.id = legal_cases.closure_requested_by
LEFT JOIN contacts as closedByContact ON closedByContact.id = legal_cases.closed_by
WHERE legal_cases.isDeleted = 0;
GO