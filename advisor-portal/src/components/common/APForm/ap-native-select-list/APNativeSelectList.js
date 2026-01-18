import React, { useState } from 'react';

import {
    FormControl,
    FormGroup,
    InputLabel,
    makeStyles,
    Select
} from '@material-ui/core';

import { handleListChange } from './../../../../APHelpers';

const APNativeSelectList = React.memo((props) => {
    var label = props?.label;
    var options = props?.options;
    var optionsLabel = props?.optionsLabel;
    var stateKey = props?.stateKey;
    var valueKey = props?.valueKey;
    var valueFromProps = props?.value;
    var multipleSelection = props?.multipleSelection;
    var changeDefaultValues = props?.changeDefaultValues;

    const [value, setValue] = useState(valueFromProps);

    const handleChange = (e) => {
        e.persist();

        let newValue = e.target.value;

        setValue(newValue);

        props.onChange(newValue);
    }

    // const handleChange = (state, event, value, valueKey, multipleSelection = false, changeDefaultValues = false) => {
    //     value = event.target.value;

    //     let defaultValues = {
    //         title: '',
    //         value: ''
    //     };

    //     let defaultValuesWithMultipleSelection = [];

    //     // This is only used to set the value in formData state
    //     let stateValue = handleListChange(event, value, valueKey, multipleSelection);

    //     if (multipleSelection && changeDefaultValues) {
    //         for (var i = 0; i < value.length; i++) {
    //             defaultValuesWithMultipleSelection.push({
    //                 title: value[i][optionsLabel],
    //                 value: value[i][valueKey]
    //             });
    //         }

    //         setValue(defaultValuesWithMultipleSelection);
    //     } else {
    //         defaultValues = value;

    //         setValue(defaultValues);
    //     }

    //     console.log("HANDLE CHANGEEEEEEE");

    //     console.log();
        
    //     props.onChange(state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues);
    // };

    const optionsHTML = options.map((item, key) => {
        return (
            <option
                key={key}
                value={item.value}
            >
                {item.label}
            </option>
        );
    });

    return (
        <FormGroup>
            <FormControl
                variant="outlined"
            >
                <InputLabel
                    id={"label-" + label}
                    htmlFor={label}
                >
                    {label}
                </InputLabel>
                <Select
                    native
                    id={"select-" + label}
                    labelId={"label-" + label}
                    label={label}
                    value={value}
                    onChange={(e) => handleChange(e)}
                    inputProps={{
                        "id": label
                    }}
                >
                    {optionsHTML}
                </Select>
            </FormControl>
        </FormGroup>
    );
});

export default APNativeSelectList;
