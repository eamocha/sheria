import React, {
    useContext,
    useState
} from 'react';
import './LitigationCaseCustomFieldsEditForm.scss';
import { FORMS_NAMES } from '../../../Constants';
import { Context } from '../../../Store';
import CustomFieldValue from '../../../api/CustomFieldValue';
import { isFunction } from '../../../APHelpers';
 
export default React.memo((props) => {
    const formId = FORMS_NAMES.litigationCaseCustomFieldsEditForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [litigationCase, ] = useState(props?.litigationCase);
    const [customFields, ] = useState(props?.litigationCaseCustomFields);

    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        CustomFieldValue.update().then((response) => {

            if (isFunction(props?.loadLegalCaseData)) {
                return props.loadLegalCaseData();
            }
        }).catch((error) => {

            let message = error?.response?.data?.message;

            if (error?.response?.data?.message === 'object') {
                message = [];

                Object.keys(error.response.data.message).map((key, index) => {
                    return error.response.data.message?.[key].forEach((item) => {
                        message.push(<p key={key}>- {error.response.data.message[key]}: {item}</p>);
                    });
                });
            }

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

            /**
             * so if the reload function is not set we have to hide the global loader,
             * otherwise, the reload function will hide the global loader itself
             */
            if (!isFunction(props?.loadLitigationCaseData)) {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            }
        });
    }

    return (
        <form
            id={formId}
            onSubmit={(e) => submit(e)}
        >

        </form>
    );
});
