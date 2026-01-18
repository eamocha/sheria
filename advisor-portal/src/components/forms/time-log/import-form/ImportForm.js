import React, {
    useContext, 
    useRef, 
    useState
} from 'react';

import './ImportForm.scss';

import { FORMS_NAMES } from './../../../../Constants';
import { useTranslation } from 'react-i18next';
import { MuiPickersUtilsProvider } from '@material-ui/pickers';
import DateFnsUtils from '@date-io/date-fns';
import {
    Context,
    initialGlobalState
} from '../../../../Store';
import {
    buildErrorMessages,
    isFunction
} from '../../../../APHelpers';
import AdvisorTimeLog from './../../../../api/AdvisorTimeLog';
import {
    Button,
    Link,
    Typography
} from '@material-ui/core';

export default React.memo((props) => {
    const { t } = useTranslation();
    const formId = FORMS_NAMES.advisorTimeLogImportForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [fileInput, setFileInput] = useState(null);
    const [errorMessage, setErroMessage] = useState("");

    const handleFileInputChange = (e) => {
        setFileInput(e.target.files[0]);
        setErroMessage("");
    }

    const submit = (e) => {
        e.preventDefault();

        if (fileInput) {
            var validExts = new Array(".xlsx", ".xls");
            var fileExt = fileInput.name.substring(fileInput.name.lastIndexOf('.'));
            
            if (validExts.indexOf(fileExt) < 0) {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: t("invalid_file_selected") + validExts.toString(),
                        severity: "error"
                    },
                    globalLoader: initialGlobalState?.globalLoader
                });

                return;
            }

            let formData = new FormData();
            formData.append("file", fileInput);

            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            AdvisorTimeLog.importExcelFile(formData).then((response) => {

                if (isFunction(globalState?.modal?.form?.submitCallback)) {
                    globalState.modal.form.submitCallback();
                }

                globalStateDispatcher({
                    modal: initialGlobalState?.modal
                });

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        severity: "success",
                        text: t("imported_successfully")
                    }
                });

            }).catch((error) => {
                let message = buildErrorMessages(error?.response?.data?.message);
                setErroMessage(message);
            }).finally(() => {
                globalStateDispatcher({
                    globalLoader: {
                        ...globalState?.globalLoader,
                        open: false
                    }
                });
                setFileInput(null);
            });
        } else {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: t("select_file_to_import"),
                    severity: "error"
                },
                globalLoader: initialGlobalState?.globalLoader
            });
        }
    }

    const downloadTemplate = (e) => {

        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorTimeLog.downloadTemplate().then(response => {
            var fileDownload = require('js-file-download');

            const regex = /filename[^;=\n]*=(?:(\\?['"])(.*?)\1|(?:[^\s]+'.*?')?([^;\n]*))/ig;
            const str = response.headers['content-disposition'];

            let m;
            let numberOfMatches = str.match(regex).length;
            let counter = 0;

            while ((m = regex.exec(str)) !== null) {
                if (counter > 2) {
                    break;
                    return false;
                }

                // This is necessary to avoid infinite loops with zero-width matches
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }

                // The result can be accessed through the `m`-variable.
                m.forEach((match, groupIndex) => {
                    console.log(`Found match, group ${groupIndex}: ${match}`);
                });

                // this is for UTF8
                if (numberOfMatches > 1 && counter > 0) {
                    fileDownload(response.data, decodeURI(m[3] ? m[3] : ''));
                } else if (numberOfMatches < 2 && counter < 1) {
                    fileDownload(response.data, m[2] ? m[2] : (m[3] ? m[3] : ''));
                }

                counter++;
            }
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

    const inputFileRef = useRef(null);
    const browseFile = () => {
        inputFileRef.current.click();
    }

    return (
        <MuiPickersUtilsProvider
            id={formId}
            utils={DateFnsUtils}
        >
            <form
                id={formId}
                onSubmit={(e) => submit(e)}
            >
                <input
                    id="import_file_input"
                    type="file" name="import_file_input"
                    multiple data-role="button"
                    accept=".xlsx, .xls"
                    class="inputfile"
                    ref={inputFileRef}
                    onChange={(e) => handleFileInputChange(e)}
                />

                <Button
                    color="primary"
                    variant="contained"
                    className="download-button"
                    onClick={(e) => browseFile(e)}>
                    {fileInput?.name ? fileInput?.name : t("choose_file")}
                </Button>

                {
                    errorMessage ?
                        <Typography
                            variant="body1"
                            color="error"
                            className="error-messages-title"
                        >
                            No records imported!
                        </Typography> : null
                }
                {
                    errorMessage ?
                        <Typography
                            variant="body1"
                            color="error"
                            className="error-messages"
                        >
                            {errorMessage}
                        </Typography> : null
                }

                <Link
                    href="#"
                    onClick={(e) => downloadTemplate(e)}
                    title={t("download_template")}
                    className="primary-link"
                    color="textPrimary"
                >
                    {t("download_template")}
                </Link>

            </form>
        </MuiPickersUtilsProvider>
    );
});
