insert into system_preferences(groupName,Keyname,KeyValue) values('MFA','mfaEnabled','true');
insert into system_preferences values('MFA','mfaChannel','Email');


alter table user_profiles ADD mfaToken varchar(7)  DEFAULT null;
alter table user_profiles ADD mfaTokenTimeCounter datetime  DEFAULT CURRENT_TIMESTAMP;;
alter table user_profiles ADD mfaTokenChecked VARCHAR(1)  DEFAULT 0
--alter table users ADD mfa_browser VARCHAR(1)  DEFAULT 0









