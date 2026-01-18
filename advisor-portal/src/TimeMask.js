export default class TimeMask {
    constructor (time, hourMinutes = 60) {
        this.time = time;

        this.hourMinutes = hourMinutes;
    }

    timeToHumanReadable = () => {
        var output = '';
        
        if (/\d*\.?\d/.test(this.time)) {
            output = this.hoursToTime(this.time);
        }

        return output;
    }

    hoursToTime = (inputHours) => {
        inputHours = parseFloat(inputHours);
        
        // Extract the remaining hours
        var remainingHours = inputHours % 1;
        var hours = parseInt(inputHours);
        var minutes = remainingHours * this.hourMinutes;
        var integer_minutes = parseInt(minutes);
        var decimal_minutes = minutes - integer_minutes <= 0.5 ? 0 : 1;
        
        minutes = (integer_minutes + decimal_minutes);
        
        var sections = {
            'h': parseFloat(hours),
            'm': parseFloat(minutes)
        };

        return this.getTimeParts(sections);
    }

    getTimeParts = (sections) => {
        // Format and return
        let timeParts = [];
        
        for (var key in sections) {
            if (sections[key] > 0) {
                timeParts.push(sections[key] + '' + key);
            }
        }

        return timeParts.join(" ");
    }
}