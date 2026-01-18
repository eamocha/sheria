<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_Users_Full_Details_View extends CI_Migration {

    private $view_name = 'users_full_details';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction

        // Full ALTER VIEW definition including the new department_id column
        $sql = "
            ALTER VIEW [dbo].[{$this->view_name}] AS
            SELECT TOP(9223372036854775800)
                users.id,
                users.isAd,
                users.user_group_id,
                users.username,
                users.email,
                users.type,
                LEFT(users.email, CHARINDEX('@', users.email)-1) AS activeDirectoryId,
                (AutthorizedUser.firstName + ' ' + AutthorizedUser.lastName) AS authorized_by,
                users.banned,
                users.ban_reason,
                users.last_ip,
                CAST(users.last_login AS DATE) AS last_login,
                CAST(users.created AS DATE) AS created,
                users.modifiedBy,
                users.userDirectory,
                (userModified.firstName + ' ' + userModified.lastName) AS userModifiedName,
                CAST(users.modified AS DATE) AS modified,
                user_profiles.flagChangePassword AS flagChangePassword,
                user_profiles.status,
                user_profiles.gender,
                user_profiles.title,
                user_profiles.firstName,
                user_profiles.lastName,
                user_profiles.father,
                user_profiles.mother,
                user_profiles.dateOfBirth,
                user_profiles.jobTitle,
                user_profiles.isLawyer,
                user_profiles.website,
                user_profiles.phone,
                user_profiles.fax,
                user_profiles.mobile,
                user_profiles.address1,
                user_profiles.address2,
                user_profiles.city,
                user_profiles.state,
                user_profiles.zip,
                user_profiles.overridePrivacy,
                user_profiles.employeeId,
                user_profiles.department_id,  -- NEW COLUMN
                user_profiles.department,
                user_profiles.ad_userCode,
                user_profiles.user_code,
                seniorityLevels.name AS seniorityLevel,
                seniorityLevels.id AS seniorityLevelId,
                user_profiles.country AS country_id,
                user_profiles.nationality AS nationality_id,
                user_groups.name AS userGroupName,
                user_groups.description AS userGroupDescription,
                providerGroup = STUFF(
                    (SELECT ', ' + provider_groups.name
                    FROM provider_groups_users
                    INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id
                    WHERE provider_groups_users.user_id = users.id
                    FOR XML PATH('')), 1, 2, ''),
                provider_group_id = STUFF(
                    (SELECT ', ' + CAST(provider_groups.id AS NVARCHAR)
                    FROM provider_groups_users
                    INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id
                    WHERE provider_groups_users.user_id = users.id
                    FOR XML PATH('')), 1, 2, ''),
                user_profiles.flagNeedApproval AS flagNeedApproval
            FROM users
            INNER JOIN user_profiles ON user_profiles.user_id = users.id
            LEFT JOIN seniority_levels seniorityLevels ON seniorityLevels.id = user_profiles.seniority_level_id
            LEFT JOIN user_groups ON user_groups.id = users.user_group_id
            LEFT JOIN user_profiles userModified ON userModified.user_id = users.modifiedBy
            LEFT JOIN users_authorizations ON users_authorizations.affectedUserId = users.id
            LEFT JOIN user_profiles AutthorizedUser ON AutthorizedUser.user_id = users_authorizations.checkerId
        ";

        $this->db->query($sql);
        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
            ALTER VIEW [dbo].[{$this->view_name}] AS
            SELECT TOP(9223372036854775800)
                users.id,
                users.isAd,
                users.user_group_id,
                users.username,
                users.email,
                users.type,
                LEFT(users.email, CHARINDEX('@', users.email)-1) AS activeDirectoryId,
                -- Removed (AutthorizedUser.firstName + ' ' + AutthorizedUser.lastName) AS authorized_by for simplicity in rollback
                users.banned,
                users.ban_reason,
                users.last_ip,
                CAST(users.last_login AS DATE) AS last_login,
                CAST(users.created AS DATE) AS created,
                users.modifiedBy,
                users.userDirectory,
                -- Removed (userModified.firstName + ' ' + userModified.lastName) AS userModifiedName for simplicity in rollback
                CAST(users.modified AS DATE) AS modified,
                user_profiles.flagChangePassword AS flagChangePassword,
                user_profiles.status,
                user_profiles.gender,
                user_profiles.title,
                user_profiles.firstName,
                user_profiles.lastName,
                user_profiles.father,
                user_profiles.mother,
                user_profiles.dateOfBirth,
                user_profiles.jobTitle,
                user_profiles.isLawyer,
                user_profiles.website,
                user_profiles.phone,
                user_profiles.fax,
                user_profiles.mobile,
                user_profiles.address1,
                user_profiles.address2,
                user_profiles.city,
                user_profiles.state,
                user_profiles.zip,
                user_profiles.overridePrivacy,
                user_profiles.employeeId,
                -- user_profiles.department_id, -- Removed in rollback
                user_profiles.department,
                user_profiles.ad_userCode,
                user_profiles.user_code,
                seniorityLevels.name AS seniorityLevel,
                seniorityLevels.id AS seniorityLevelId,
                user_profiles.country AS country_id,
                user_profiles.nationality AS nationality_id,
                user_groups.name AS userGroupName,
                user_groups.description AS userGroupDescription,
                providerGroup = STUFF(
                    (SELECT ', ' + provider_groups.name
                    FROM provider_groups_users
                    INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id
                    WHERE provider_groups_users.user_id = users.id
                    FOR XML PATH('')), 1, 2, ''),
                provider_group_id = STUFF(
                    (SELECT ', ' + CAST(provider_groups.id AS NVARCHAR)
                    FROM provider_groups_users
                    INNER JOIN provider_groups ON provider_groups.id = provider_groups_users.provider_group_id
                    WHERE provider_groups_users.user_id = users.id
                    FOR XML PATH('')), 1, 2, ''),
                user_profiles.flagNeedApproval AS flagNeedApproval
            FROM users
            INNER JOIN user_profiles ON user_profiles.user_id = users.id
            LEFT JOIN seniority_levels seniorityLevels ON seniorityLevels.id = user_profiles.seniority_level_id
            LEFT JOIN user_groups ON user_groups.id = users.user_group_id
            -- Removed joins to userModified, users_authorizations, and AutthorizedUser for cleaner rollback
        ";


        $this->db->query($sql);
        $this->db->trans_complete(); // Complete the transaction
    }
}
