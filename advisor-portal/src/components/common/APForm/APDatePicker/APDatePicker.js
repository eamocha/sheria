import React, { useRef, useState } from 'react';
import './APDatePicker.scss';
import {
    FormGroup,
    FormControl
} from '@material-ui/core';
import { KeyboardDatePicker } from '@material-ui/pickers';
import { formatDate } from '../../../../APHelpers';

export default React.memo((props) => {
    var label = props?.label;
    var stateKey = props?.stateKey;
    var valueFromProp = props?.value;

    const [value, setValue] = useState(valueFromProp);
    const [open, setOpen] = useState(false);

    const handleClick = () => {
        setOpen(true);
    }

    const handleChange = (date) => {
        let dateValue = date === null ? null : formatDate(date);

        setValue(dateValue);

        props.handleChange(stateKey, date);
    };

    const handleClose = () => {
        setOpen(false);
    }

    return (
        <FormGroup>
            <FormControl>
                <KeyboardDatePicker
                    {...props}
                    label={label}
                    inputVariant="outlined"
                    autoOk
                    variant="inline"
                    value={value}
                    open = {open}
                    onClick={() => handleClick()}
                    onChange={(date) => handleChange(date)}
                    onClose={() => handleClose()}
                    inputadornmentprops={{ position: "end" }}
                />
            </FormControl>
        </FormGroup>
    );
});