(function ( $ ) {
    $.fn.timemask = function(options){

        var settings = jQuery.extend({
            time: 0,
            hourMinutes: 60
        }, options);

        function timeToHumanReadable(){
            var output = '';

            if(/\d*\.?\d/.test(settings.time)){
                    output = hoursToTime(settings.time);
            }

            return output;
        }

        function hoursToTime(inputHours) {
            inputHours = parseFloat(inputHours);
            // Extract the remaining hours
            var remainingHours = inputHours % 1;
            var hours = parseInt(inputHours);
            var minutes = remainingHours * settings.hourMinutes;
            var integer_minutes = parseInt(minutes);
            var decimal_minutes = minutes - integer_minutes <= 0.5 ? 0 : 1;
            minutes = (integer_minutes + decimal_minutes);
            var sections = {
                'h': parseFloat(hours),
                'm': parseFloat(minutes)
            };
            return getTimeParts(sections);
        }

        function getTimeParts(sections){
            // Format and return
            timeParts = [];

            for(var key in sections){
                if(sections[key] > 0){
                    timeParts.push(sections[key] + '' + key);
                }
            }

            return timeParts.join(" ");
        }

        return timeToHumanReadable();
    };
}(jQuery));