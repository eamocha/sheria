import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './ProfileForm.scss';

import {
    Button,
    Divider,
    FormGroup,
    Grid,
    Typography
} from '@material-ui/core';

import {
    TextValidator,
    ValidatorForm
} from 'react-material-ui-form-validator';

import AdvisorUser from '../../../api/AdvisorUser';

import {
    Context,
    initialGlobalState
} from '../../../Store';

import { buildErrorMessages } from '../../../APHelpers';
import { SESSION_KEYS } from '../../../Constants';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [t] = useTranslation();

    const [formData, setFormData] = useState({
        firstName: globalState?.user?.data?.firstName,
        lastName: globalState?.user?.data?.lastName,
        email: globalState?.user?.data?.email,
        address: globalState?.user?.data?.address,
        companyName: globalState?.user?.data?.company_name,
        phone: globalState?.user?.data?.phone,
        mobile: globalState?.user?.data?.mobile,
        jobTitle: globalState?.user?.data?.jobTitle,
        old_password: '',
        password: '',
        confirm_password: '',
    });

    const [oldPasswordIsWrong, setOldPasswordIsWrong] = useState(false);

    useEffect(() => {
        ValidatorForm.addValidationRule('isPasswordMatch', (value) => {
            if (value !== formData.password) {
                return false;
            }

            return true;
        });

        ValidatorForm.addValidationRule('requiredIfOldPasswordIsSet', (value) => {
            if (formData.old_password.length > 0 && value.length <= 0) {
                return false;
            }

            return true;
        });

        ValidatorForm.addValidationRule('requiredIfPasswordIsSet', (value) => {
            if (formData.password.length > 0 && value.length <= 0) {
                return false;
            }

            return true;
        });

        ValidatorForm.addValidationRule('oldPasswordIsWrong', () => {

            return !oldPasswordIsWrong;
        });
    }, [formData, oldPasswordIsWrong]);

    const handleFormDataChange = (e, stateKey) => {
        e.persist();

        setFormData((prevState) => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const submit = (e) => {
        let data = prepFormData();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorUser.update(globalState?.user?.data?.id, data).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Your profile has been updated successfully"
                }
            });

            var userData = response?.data?.data;
            userData.company_name = globalState?.user?.data?.company_name;

            if (response?.data?.passwordUpdated === false && formData.old_password.length > 0) {
                setOldPasswordIsWrong(true);
            } else {
                setOldPasswordIsWrong(false);
            }

            globalStateDispatcher({
                user: {
                    ...globalState?.user,
                    data: {
                        ...globalState?.user?.data,
                        firstName: userData.firstName,
                        lastName: userData.lastName,
                        email: userData.email,
                        address: userData.address,
                    }
                }
            });

            setFormData((prevState) => ({
                ...prevState,
                old_password: '',
                password: '',
                confirm_password: ''
            }));

            sessionStorage.setItem(SESSION_KEYS.user, JSON.stringify(userData));
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                },
                globalLoader: initialGlobalState?.globalLoader
            });
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const prepFormData = () => {
        let result = {};

        for (let [key, value] of Object.entries(formData)) {
            if (value !== null) {
                result[key] = value;
            }
        }

        return result;
    }

    useEffect(() => {
        loadData();
    }, []);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorUser.get(JSON.parse(sessionStorage.getItem('A4L-AP-user')).id).then((response) => {
            response.data.data.company_name = response?.data?.data?.company.name;

            sessionStorage.setItem(SESSION_KEYS.user, JSON.stringify(response?.data?.data));
         
            globalStateDispatcher({
                user: {
                    ...globalState?.user,
                    data: response?.data?.data
                }
            });

            setFormData({
                firstName: response?.data?.data.firstName,
                lastName: response?.data?.data.lastName,
                email: response?.data?.data.email,
                address: response?.data?.data.address,
                companyName: response?.data?.data?.company.name,
                phone: response?.data?.data.phone,
                mobile: response?.data?.data.mobile,
                jobTitle: response?.data?.data.jobTitle,
                old_password: '',
                password: '',
                confirm_password: '',
            });

        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    return (
        <ValidatorForm
            id="user-profile-form"
            onSubmit={(e) => submit(e)}
        >
            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("first_name")}
                            variant="outlined"
                            value={formData.firstName}
                            required
                            onChange={(e) => handleFormDataChange(e, 'firstName')}
                            validators={['required']}
                            errorMessages={['This field is required']}
                        />
                    </FormGroup>
                </Grid>
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("last_name")}
                            variant="outlined"
                            value={formData.lastName}
                            required
                            onChange={(e) => handleFormDataChange(e, 'lastName')}
                            validators={['required']}
                            errorMessages={['This field is required']}
                        />
                    </FormGroup>
                </Grid>
            </Grid>
            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("email_address")}
                            variant="outlined"
                            value={formData.email}
                            required
                            onChange={(e) => handleFormDataChange(e, 'email')}
                            validators={['required', 'isEmail']}
                            errorMessages={['This field is required', 'E-mail Address not valid']}
                        />
                    </FormGroup>
                </Grid>
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            name="CompanyName"
                            label={t("company")}
                            variant="outlined"
                            value={formData.companyName}
                            disabled
                        />
                    </FormGroup>

                </Grid>
            </Grid>


            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("phone")}
                            variant="outlined"
                            type="number"
                            value={formData.phone}
                            onChange={(e) => handleFormDataChange(e, 'phone')}
                        />
                    </FormGroup>
                </Grid>
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("mobile")}
                            variant="outlined"
                            type="number"
                            value={formData.mobile}
                            onChange={(e) => handleFormDataChange(e, 'mobile')}
                        />
                    </FormGroup>

                </Grid>
            </Grid>

            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("job_title")}
                            variant="outlined"
                            value={formData.jobTitle}
                            onChange={(e) => handleFormDataChange(e, 'jobTitle')}
                        />
                    </FormGroup>

                </Grid>
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            name="Address"
                            label={t("address")}
                            variant="outlined"
                            value={formData.address}
                            onChange={(e) => handleFormDataChange(e, 'address')}
                            inputProps={{
                                autoComplete: 'new-password',
                                form: {
                                    autoComplete: 'off',
                                },
                            }}
                        />
                    </FormGroup>
                </Grid>
            </Grid>
            <Divider
                className="divider"
            />
            <Grid
                container
                className="user-profile-form-row"
            >
                <Typography
                    variant="subtitle1"
                >
                    {t('change_password')}
                </Typography>
            </Grid>
            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("old_password")}
                            variant="outlined"
                            type="password"
                            error={oldPasswordIsWrong}
                            helperText={oldPasswordIsWrong ? "Old Password not match, please try again" : ""}
                            value={formData.old_password}
                            onChange={(e) => handleFormDataChange(e, 'old_password')}
                            validators={['requiredIfPasswordIsSet']}
                            errorMessages={['This field is required when Password is not empty']}
                            inputProps={{
                                autoComplete: 'new-password',
                                form: {
                                    autoComplete: 'off',
                                },
                            }}
                        />
                    </FormGroup>
                </Grid>
            </Grid>
            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("new_password")}
                            variant="outlined"
                            type="password"
                            value={formData.password}
                            onChange={(e) => handleFormDataChange(e, 'password')}
                            validators={['requiredIfOldPasswordIsSet']}
                            errorMessages={['This field is required when Old Password is not empty']}
                            inputProps={{
                                autoComplete: 'new-password',
                                form: {
                                    autoComplete: 'off',
                                },
                            }}
                        />
                    </FormGroup>
                </Grid>
            </Grid>
            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <TextValidator
                            label={t("confirm_password")}
                            variant="outlined"
                            type="password"
                            value={formData.confirm_password}
                            onChange={(e) => handleFormDataChange(e, 'confirm_password')}
                            validators={['isPasswordMatch', 'requiredIfPasswordIsSet']}
                            errorMessages={['Password mismatch', 'This field is required']}
                            inputProps={{
                                autoComplete: 'new-password',
                                form: {
                                    autoComplete: 'off',
                                },
                            }}
                        />
                    </FormGroup>
                </Grid>
            </Grid>
            <Grid
                container
                className="user-profile-form-row"
            >
                <Grid
                    item
                    sm={6}
                    className={"user-profile-form-row-item " + globalState.domDirection}
                >
                    <FormGroup>
                        <Button
                            variant="contained"
                            color="primary"
                            type="submit"
                            className="submit"
                        >
                            {t("save")}
                        </Button>
                    </FormGroup>
                </Grid>
                <Grid
                    item
                    sm={6}
                />
            </Grid>
        </ValidatorForm>
    );
});
