import React from 'react';

import {
    parseISO,
    isValid,
    format,
    millisecondsToHours,
    millisecondsToMinutes,
    millisecondsToSeconds
} from 'date-fns';

export function handleListChange(event, value, valueKey, multipleSelection = false) {
    event.persist();

    if (multipleSelection) {
        let values = [];

        for (var i = 0; i < value.length; i++) {
            values.push(value[i][valueKey]);
        }

        value[valueKey] = values;
    }

    return value !== null && value[valueKey] !== null ? value[valueKey] : '';
}

export function loadUsersList(data) {
    let result = [];

    for (var i = 0; i < data.length; i++) {
        let item = data[i];

        result.push({
            title: item?.firstName + ' ' + item?.lastName,
            value: item?.id
        });
    }

    return result;
}

export function loadListWithLanguages(data, languagesKey, titleKey, valueKey, currentItem = null) {
    let result = {
        options: [],
        defaultItem: null,
        defaultItemValue: null,
        currentItem: null
    };

    for (var i = 0; i < data.length; i++) {
        let item = data[i];
        let languages = item[languagesKey];

        for (var j = 0; j < languages.length; j++) {
            let itemLanguage = languages[j];

            if (itemLanguage.language_id === 1) {
                let itemObj = {
                    title: itemLanguage[titleKey],
                    value: item[valueKey]
                };

                result.options.push(itemObj);

                if (i === 0) {
                    result.defaultItem = itemObj;
                    result.defaultItemValue = itemObj.value;
                }

                if (currentItem === item.id) {
                    result.currentItem = itemObj;
                }
            }
        }
    }

    result.options.sort(compare);

    return result;
}

function compare(a, b) {
    if (a.title < b.title) {
        return -1;
    }
    if (a.title > b.title) {
        return 1;
    }
    return 0;
}

export function formatDate(date) {
    return isValid(date) ?
        (date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + date.getDate())
        :
        null;
}

export function formatTime(date) {
    return isValid(date) ?
        date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds()
        :
        null;
}

export function formatMSTime(milliseconds) {
    return millisecondsToHours(milliseconds).toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false })
        + ":" + (millisecondsToMinutes(milliseconds) % 60).toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false })
        + ":" + (millisecondsToSeconds(milliseconds) % 60).toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false });
}

export function formatDateTime(date) {
    return isValid(date) ?
        format(date, 'yyyy-MM-dd HH:mm')
        :
        null
}

export function isFunction(functionToCheck) {
    return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

export function addEllipsis(str, length = 100) {
    return str && str.length > 0 ? (str.substring(0, length) + (str.length >= length ? '...' : '')) : '';
}

export function stringTruncate(str, n) {
    return str ? str.substr(0, n - 1) + (str.length > n ? '...' : '') : null;
}

export function getAdvisorUserFullName(advisor, isAdvisor = true) {
    if (!advisor)
        return '';

    if (isAdvisor) {
        return advisor?.firstName + ' ' + advisor?.lastName
    }

    return advisor?.user_profile?.firstName + ' ' + advisor?.user_profile?.lastName;
}

export function loadAdvisorUsersList(data) {
    let result = [];

    for (var i = 0; i < data.length; i++) {
        let item = data[i];

        result.push({
            title: getAdvisorUserFullName(item),
            value: item?.id
        });
    }

    return result;
}

export function getValueFromLanguage(data, languagePropertyName, languageId, result = '', languageFieldName = 'name') {
    if (data && data?.[languagePropertyName]) {
        for (var i = 0; i < data?.[languagePropertyName].length; i++) {
            let language = data[languagePropertyName][i];

            if (language?.language_id === languageId) {
                result = language?.[languageFieldName];

                break;
            }
        }
    }

    return result;
}

export function defaultLoadList(data, titleKey, valueKey) {
    let options = [];

    for (var i = 0; i < data.length; i++) {
        let item = data[i];

        options.push({
            title: item?.[titleKey],
            value: item?.[valueKey]
        });
    }

    return options;
}

export function containsObject(obj, list) {
    var i;
    for (i = 0; i < list.length; i++) {
        if (list[i] === obj) {
            return true;
        }
    }

    return false;
}

export const concatArrays = (...arrays) => {

    return [].concat(...arrays.filter(Array.isArray));
}

export const uniquedArray = (array) => {

    return [...new Set(array)];
}

export function getModulePrefix(module, moduleRecordId = '') {
    let result = '';

    moduleRecordId = moduleRecordId.toString();

    switch (module) {
        case 'legal-case':
            result = 'M';
            break;

        case 'hearing':
            result = 'H';
            break;

        case 'advisor-task':
            result = 'T';
            break;

        default:
            break;
    }

    // let id = moduleRecordId.padStart(8, '0');
    let id = moduleRecordId;

    result += id;

    return result;
}

export function buildErrorMessages(messageObject) {
    messageObject = messageObject ?? '';

    let message = messageObject !== null ? messageObject : 'Error';

    if (typeof messageObject === 'object') {
        message = [];

        Object.keys(messageObject).map((key, index) => {
            if (messageObject?.[key] instanceof Array) {
                return messageObject?.[key].forEach((item) => {
                    message.push(<p key={key}>- {key}: {item}</p>);
                });
            } else if (typeof messageObject?.[key] === 'string') {
                return message.push(<p key={key}>- {key}: {messageObject?.[key]}</p>);
            }

            return '';
        });
    }

    return message;
}

// export function isValidDate(date) {
//     return isValid(parseISO(date));
// }
export function generateListOfYears(startYear, endYear) {
    var years = [];

    startYear = startYear || 2005;
    endYear = endYear || 2045;

    while (startYear <= endYear) {
        years.push(startYear++);
    }

    return years;
}

export function generateListOfNumbers(startNum, endNum) {
    var nums = [];

    startNum = startNum || 1;
    endNum = endNum || 12;

    while (startNum <= endNum) {
        nums.push(startNum++);
    }

    return nums;
}

export function concatStrings(strs, separator = ' ') {
    let strNotEmpty = false;
    let result = '';

    for (var i = 0; i < strs.length; i++) {
        let str = strs[i];

        if (str) {
            strNotEmpty = true;

            result += str;

            if (i + 1 < strs.length) {
                result += separator;
            }
        }
    }

    return strNotEmpty ? result : '';
}

export function buildInstanceURL() {
    if (!process.env.NODE_ENV || process.env.NODE_ENV === 'development') {
        return process.env.PUBLIC_URL;
    }

    //  return '/Sheria360/External-Counsel-Portal';

    let pathname = window.location.pathname.split('/');

    return ('/' + pathname[1]);
}

export function getFileExtenstion(file) {
    if (!file)
        return null;

    const name = file.name;
    const lastDot = name.lastIndexOf('.');
    const ext = name.substring(lastDot + 1);
    return ext;
}