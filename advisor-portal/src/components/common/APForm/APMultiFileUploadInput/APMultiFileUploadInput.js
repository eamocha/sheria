import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './APMultiFileUploadInput.scss';
import {
    FormGroup,
    FormControl,
    FormLabel,
    Grid,
    Button,
} from '@material-ui/core';
import ClearIcon from '@material-ui/icons/Clear';
import AttachFileIcon from '@material-ui/icons/AttachFile';
import { useTranslation } from 'react-i18next';
import { ALLOWED_UPLOAD_EXT } from '../../../../Constants';
import { Context } from '../../../../Store';
import { getFileExtenstion } from '../../../../APHelpers';

export default React.memo((props) => {

    const { t } = useTranslation();
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [fileInputs, setFileInputs] = useState([
        {
            id: 'file-1',
            value: null
        }
    ]);

    useEffect(() => {
        props.handleFilesChange(fileInputs.filter(item => {
            return item.value != null
        }));
    }, [fileInputs]);

    const [fileInputsCounter, setFileInputsCounter] = useState(1);

    const handleFileInputChange = (e, fileInput) => {
        let fileExt = getFileExtenstion(e.target.files[0]);
        if (ALLOWED_UPLOAD_EXT.indexOf(fileExt) == -1) {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "File extension not allowed",
                    severity: "error"
                }
            });
            return;
        }

        let tmpFileInputs = fileInputs.map(item => {
            if (item.id === fileInput.id) {
                item.value = e.target.files[0];
            }

            return item
        });

        setFileInputs(tmpFileInputs);
    };

    const removeFileInput = (fileInput) => {
        if (fileInputsCounter == 1) {
            setFileInputs([
                {
                    id: 'file-1',
                    value: null
                }
            ]);

        } else {
            setFileInputs(prevState => [
                ...prevState.filter(item => {
                    return item.id != fileInput.id
                })
            ]);

            setFileInputsCounter(prevState => prevState - 1);
        }

    };

    const addFileInput = () => {
        let oldCounter = fileInputsCounter;

        setFileInputs(prevState => [
            ...prevState,
            {
                id: 'file-' + (oldCounter + 1),
                value: null
            }
        ]);

        setFileInputsCounter(prevState => prevState + 1);
    };

    let files = fileInputs.map(item => {
        return (
            <FormControl
                key={item.id}
                className="AP-multi-file-upload-input-row"
            >
                <Grid
                    container
                >
                    <Grid item sm={11}>
                        {/* <Input
                            id={item.id}
                            type="file"
                            onChange={(e) => handleFileInputChange(e, item)}
                        /> */}
                        {/* <input
                            id={item.id}
                            type="file"
                            name="file"
                            onChange={(e) => handleFileInputChange(e, item)}
                            class="inputfile" />
                        <label for="file">Choose a file</label> */}
                        <input
                            id={item.id}
                            type="file" name={"file" + item.id}
                            multiple data-role="button"
                            data-inline="true" data-mini="true"
                            data-corners="false"
                            class="inputfile"
                            onChange={(e) => handleFileInputChange(e, item)}
                        />
                        <label
                            for={item.id} data-role="button"
                            data-inline="true" data-mini="true"
                            data-corners="false"> {item?.value && item?.value?.name ? item?.value?.name : t("choose_file")}
                        </label>

                    </Grid>
                    <Grid
                        item
                        sm={1}
                    >
                        <Button
                            onClick={() => removeFileInput(item)}
                            className="remove-btn"
                        >
                            <ClearIcon />
                        </Button>
                    </Grid>
                </Grid>
            </FormControl>
        );
    });

    return (
        <FormGroup
            className="AP-multi-file-upload-input"
        >
            <FormLabel>
                <AttachFileIcon className="attach-file-icon" /> {t("attach_files")}
            </FormLabel>
            {files}
            <Button
                className="AP-multi-file-upload-input-add-more-btn"
                onClick={() => addFileInput()}
            >
                {t("add_more")}
            </Button>
        </FormGroup>
    );
});
