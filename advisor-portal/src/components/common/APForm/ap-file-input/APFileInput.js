import React, { useContext, useState } from 'react';

import './APFileInput.scss';

import {
    FormGroup,
    FormControl,
    Input,
    FormLabel
} from '@material-ui/core';
import { Context } from '../../../../Store';
import { ALLOWED_UPLOAD_EXT } from '../../../../Constants';
import { getFileExtenstion } from '../../../../APHelpers';
 
export default React.memo((props) => {
    var valueFromProp = props?.value ?? '';
    var label = props?.label ?? '';

    const [globalState, globalStateDispatcher] = useContext(Context);
    const [value, setValue] = useState(valueFromProp);

    const handleChange = (e) => {
        e.persist();

        e.target.isAllowed = true;

        let fileExt = getFileExtenstion(e?.target?.files[0]);
        if (ALLOWED_UPLOAD_EXT.indexOf(fileExt) == -1) {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "File extension not allowed",
                    severity: "error"
                }
            });
            e.target.isAllowed = false;
        }

        setValue(e?.target?.value);
        props.handleChange(e);
    };

    return (
        <FormGroup>
            <FormLabel>
                {label}
            </FormLabel>
            <FormControl>
                <Input
                    {...props}
                    type="file"
                    variant="outlined"
                    value={value}
                    onChange={(e) => handleChange(e)}
                />
            </FormControl>
        </FormGroup>
    );
});
