
UPDATE instance_data SET keyValue = 'on-server' WHERE keyName = 'installationType';
GO
UPDATE system_preferences SET keyValue ='no' WHERE keyName = 'AllowFeatureCustomerPortal';
GO
UPDATE users SET password = '$1$BCwY504G$n048UMm1yqNGiN4zFGpsl0' WHERE id = '0000000001';
GO
UPDATE system_preferences SET keyValue ='no' WHERE keyName = 'use_a4l_smtp';
GO