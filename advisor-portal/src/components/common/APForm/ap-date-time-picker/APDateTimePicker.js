import React, { useState } from 'react';
import './APDateTimePicker.scss';
import {
    FormGroup,
    FormControl
} from '@material-ui/core';
import { KeyboardDateTimePicker } from '@material-ui/pickers';

export default React.memo((props) => {
    var label = props?.label;
    var stateKey = props?.stateKey;
    var valueFromProp = props?.value;

    const [value, setValue] = useState(valueFromProp);

    const handleChange = (date) => {
        setValue(date);

        props.handleChange(stateKey, date);
    };

    return (
        <FormGroup>
            <FormControl>
                <KeyboardDateTimePicker
                    {...props}
                    label={label}
                    inputVariant="outlined"
                    autoOk
                    variant="outlined"
                    value={value}
                    onChange={(date) => handleChange(date)}
                    inputadornmentprops={{ position: "end" }}
                />
            </FormControl>
        </FormGroup>
    );
});
