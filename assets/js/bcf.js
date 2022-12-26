var productPrice = 0;
var $ = jQuery.noConflict();
$(document).ready(function() {

    //hide previous price and get price for on change calculation
	$('#bcf-final-price').hide();
    if($('.bcf-product-id').prop('id') !== 'bcf-'){
        productPrice = parseInt($('.bcf-product-id').text());
    }

    bcdSizeDescription();
    $('#bcf-meter-field , #bcf-centimeter-field').on('change paste keyup',function () {
        $('#bcf-show-size').empty();
        bcdSizeDescription();
    })

});

// calculate and update price on change of inputs in 4 conditions
function bcdSizeDescription() {
    var meterValue = $('#bcf-meter-field').val();
    var centimeterValue = $('#bcf-centimeter-field').val();
    sizeString ='';
    finalPrice = '';
    $('#bcf-final-price').empty();

    if(centimeterValue === '' && meterValue==='') {
        var sizeString = 'متراژ مشخص نشده است.';
    } else if(meterValue==='') {
        var sizeString = 'متراژ: '+centimeterValue+' سانتیمتر x  هر متر: '+productPrice+' تومان = ';
        finalPrice = ((productPrice/100)*parseInt(centimeterValue)).toPrecision();
		$('#bcf-final-price').show();
    } else if (centimeterValue === '') {
		var sizeString = 'متراژ: '+meterValue+' متر x  هر متر: '+productPrice+' تومان = ';
        finalPrice = ((productPrice)*parseInt(meterValue)).toPrecision();
		$('#bcf-final-price').show();
    } else {
        var sizeString = 'متراژ: '+meterValue+' متر و '+centimeterValue+' سانتیمتر x  هر متر: '+productPrice+' تومان = ';
        finalPrice = ((productPrice)*parseInt(meterValue) + (productPrice/100)*parseInt(centimeterValue)).toPrecision();
		$('#bcf-final-price').show();
    } 

    $('#bcf-show-size').append(sizeString);
    $('#bcf-final-price').append('قیمت نهایی: '+finalPrice + ' تومان');
}