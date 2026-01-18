import React, { useState } from 'react';
import './APTextFieldInput.scss';
import {
    FormGroup,
    TextField,
    FormControl
} from '@material-ui/core';

export default React.memo((props) => {
    var stateKey = props?.stateKey ?? '';
    var valueFromProp = props?.value ?? '';

    const [value, setValue] = useState(valueFromProp);

    const handleChange = (e) => {
        e.persist();

        setValue(e?.target?.value);

        props.handleChange(e, stateKey);
    };

    return (
        <FormGroup>
            <FormControl>
                <TextField
                    {...props}
                    variant="outlined"
                    value={value}
                    onChange={(e) => handleChange(e)}
                />
            </FormControl>
        </FormGroup>
    );
});
