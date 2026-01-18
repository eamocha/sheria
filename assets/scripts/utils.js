export function isTrue(value) {
    if (typeof (value) === 'string') {
        value = value.trim().toLowerCase();
    }
    switch (value) {
        case true:
        case "true":
        case 1:
        case "1":
        case "on":
        case "yes":
            return true;
        default:
            return false;
    }
}

export function dateWithoutTime(date) {
    date = new Date(date);
    date.setDate(date.getDate() + 1);
    return date.toISOString().substr(0, 10);
}

export function displayListString(string, delimiter) {
    if (string && string.length > 0) {
        let dataToArray = string.split(delimiter).map(item => item.trim());
        return dataToArray.join("\n");
    }
    return "";
}

export function trimHtmlTags(string) {
    if (string && string.length > 0) {
        return string.replace(/<[^>]*>?/gm, '');
    }
    return "";
}

export function limitCharacters(string) {
    const limit = 100;
    if (!string) {
        return "";
    }
    if (string.length > limit) {
        return string.substring(0, 100) + "...";
    }
    return string;
}