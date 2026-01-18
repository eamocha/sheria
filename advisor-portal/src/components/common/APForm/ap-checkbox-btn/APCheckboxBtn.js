import React, { useState } from 'react';

import './APCheckboxBtn.scss';

import {
    FormGroup,
    FormControl,
    FormControlLabel,
    Checkbox
} from '@material-ui/core';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    var label = props?.label;
    var stateKey = props?.stateKey;
    var valueFromProp = props?.value;
    var checkedValue = props?.checkedValue;
    var uncheckedValue = props?.uncheckedValue;

    const [value, setValue] = useState(valueFromProp);
    const [t] = useTranslation();

    const handleChange = (e) => {
        e.persist();

        setValue(prevState => {
            return (
                checkedValue ? (e?.target?.value === checkedValue ? checkedValue : uncheckedValue) : !prevState
            )
        });

        props.handleChange(e, stateKey);
    };

    return (
        <FormGroup>
            <FormControl>
                <FormControlLabel
                    title={props?.title ? t(props?.title) : ""}
                    control={
                        <Checkbox
                            {...props}
                            checked={checkedValue ? value === checkedValue : value}
                            onChange={(e) => handleChange(e)}
                            value={checkedValue ? (value === checkedValue ? uncheckedValue : checkedValue) : !value}
                        />
                    }
                    label={label}
                />
            </FormControl>
        </FormGroup>
    );
});
