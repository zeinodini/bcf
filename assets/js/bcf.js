var $ = jQuery.noConflict();
$(document).ready(function() {
        var meterValue = $('#bcf-meter-field').val();
        var centimeterValue = $('#bcf-centimeter-field').val();
        var sizeString = 'متر و '+centimeterValue+' سانتیمتر' + meterValue;
        $('#bcf-show-size').append(sizeString);

        $('#bcf-meter-field , #bcf-centimeter-field').on('change',function () {
            $('#bcf-show-size').empty();
            var meterValue = $('#bcf-meter-field').val();
            var centimeterValue = $('#bcf-centimeter-field').val();
            var sizeString = 'متر و '+centimeterValue+' سانتیمتر' + meterValue;
            $('#bcf-show-size').append(sizeString);
        })

});