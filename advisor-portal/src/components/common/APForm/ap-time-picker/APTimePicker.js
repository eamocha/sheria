import React, { useState } from 'react';

import './APTimePicker.scss';

import { KeyboardTimePicker } from '@material-ui/pickers';

import { isDate } from 'date-fns';

export default React.memo((props) => {
    var label = props?.label;
    var stateKey = props?.stateKey;
    var valueFromProp = props?.value;

    const [value, setValue] = useState(valueFromProp);

    const handleChange = (date) => {
        let dateValue = date === null ? null : (isDate(date) ? date : null);

        setValue(dateValue);

        props.handleChange(stateKey, date, true);
    };

    return (
        <KeyboardTimePicker
            {...props}
            label={label}
            inputVariant="outlined"
            autoOk
            variant="inline"
            value={value}
            onChange={(time) => handleChange(time)}
        />
    );
});