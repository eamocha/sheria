import React, { useState } from 'react';
import './APAutocompleteList.scss';
import {
    FormGroup,
    FormControl,
    TextField,
    InputAdornment
} from '@material-ui/core';
import { Autocomplete } from '@material-ui/lab';
import SearchIcon from '@material-ui/icons/Search';
import { handleListChange } from '../../../../APHelpers';
import PropTypes from 'prop-types';

const APAutocompleteList = React.memo((props) => {
    var label = props?.label
    var options = props?.options;
    var optionsLabel = props?.optionsLabel;
    var optionsLabelArray = props?.optionsLabelArray ?? [];
    var stateKey = props?.stateKey;
    var valueKey = props?.valueKey;
    var valueFromProps = props?.value;
    var multipleSelection = props?.multipleSelection;
    var changeDefaultValues = props?.changeDefaultValues;

    const [value, setValue] = useState(valueFromProps);

    const handleChange = (state, event, selectedObject, valueKey, multipleSelection = false, changeDefaultValues = false) => {

        if (!selectedObject) {
            event.target.value = "";
            handleTextChange(event);
        }

        let defaultValues = {
            title: '',
            value: ''
        };

        let defaultValuesWithMultipleSelection = [];

        // This is only used to set the value in formData state
        let stateValue = handleListChange(event, selectedObject, valueKey, multipleSelection);

        if (multipleSelection && changeDefaultValues) {
            for (var i = 0; i < selectedObject.length; i++) {
                let title = "";
                if (optionsLabelArray.length > 0) {
                    for (var j = 0; j < optionsLabelArray.length; j++) {
                        title += selectedObject[i][optionsLabelArray[j]];
                        if (j < optionsLabelArray.length - 1) {
                            title += " - "
                        }
                    }
                }
                else {
                    title = selectedObject[i][optionsLabel];
                }
                defaultValuesWithMultipleSelection.push({
                    title: title,
                    value: selectedObject[i][valueKey]
                });

            }

            var flags = [], output = [], l = defaultValuesWithMultipleSelection.length, i;
            for (i = 0; i < l; i++) {
                if (flags[defaultValuesWithMultipleSelection[i].value]) continue;
                flags[defaultValuesWithMultipleSelection[i].value] = true;
                output.push(defaultValuesWithMultipleSelection[i]);
            }

            defaultValuesWithMultipleSelection = output;
            setValue(defaultValuesWithMultipleSelection);
        } else {
            defaultValues = selectedObject;

            setValue(defaultValues);
        }

        // this param is used to pass any additional data
        let additionalData = {
            selectedObject: selectedObject
        };

        props.onChange(state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues, additionalData);
    };

    const [timeoutValue, setTimeoutValue] = useState(null);

    const handleTextChange = (e) => {
        e.persist();
        if (timeoutValue)
            clearTimeout(timeoutValue)

        setTimeoutValue(setTimeout(() => {
            if (props?.textOnChange)
                props.textOnChange(e);
        }, 1000));
    }


    const getTitle = (option) => {
        let title = "";
        if (optionsLabelArray.length > 0) {

            for (var i = 0; i < optionsLabelArray.length; i++) {
                title += option[optionsLabelArray[i]] ?? '';
                if (i < optionsLabelArray.length - 1 && title !== '') {
                    title += ' - '
                }
            }
        }
        else {
            title = option[optionsLabel] ?? '';
        }
        return title;
    }

    return (
        <FormGroup
            className="ap-autocomplete-list"
            style={{
                display: typeof props.show !== 'undefined' ? (props.show === true ? 'block' : 'none') : 'block'
            }}
        >
            <FormControl
                className="ap-autocomplete-list-form-control"
            >
                <Autocomplete
                    {...props}
                    label={label}
                    variant="outlined"
                    autoComplete
                    options={options}
                    getOptionLabel={option => getTitle(option)}
                    onChange={(event, value) => handleChange(stateKey, event, value, valueKey, multipleSelection, changeDefaultValues)}
                    validators={['required']}
                    value={value}
                    renderInput={
                        params => (
                            <TextField
                                {...params}
                                label={label}
                                variant={props?.variant ? props?.variant : "outlined"}
                                fullWidth
                                onChange={(event) => handleTextChange(event)}
                                icon={props?.textOnChange ? <SearchIcon /> : null}
                                required={props.textRequired}
                            />
                        )
                    }
                    renderOption={props?.renderOption ? props?.renderOption : option => {
                        return <span>{getTitle(option)}</span>
                    }
                    }
                    inputadornmentprops={{
                        endAdornment: (
                            <InputAdornment position="end">
                                <SearchIcon />
                            </InputAdornment>
                        ),
                    }}
                />
            </FormControl>
        </FormGroup>
    );
});

APAutocompleteList.propTypes = {
    label: PropTypes.string,
    required: PropTypes.bool,
    textRequired: PropTypes.bool,
    options: PropTypes.array.isRequired,
    optionsLabel: PropTypes.string.isRequired,
    optionsLabelArray: PropTypes.array,
    stateKey: PropTypes.string.isRequired,
    value: PropTypes.object.isRequired,
    valueKey: PropTypes.string.isRequired,
    onChange: PropTypes.func.isRequired,
};

export default APAutocompleteList;
