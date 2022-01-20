jQuery(document).ready(function ($) {
	var dataset = dataList.dataset;
	dataset['value'] = currentSelectedOption.value;

	$.ajax({
		url: personalizedSupport.ajaxUrl,
		data: {
			'action': personalizedSupport.sendFeedback,
			'dataset': dataset,
			'nonce': personalizedSupport.nonce
		},
		success: function (data) {
			// This outputs the result of the ajax request
			console.log(data);
		},
		error: function (errorThrown) {
			console.log(errorThrown);
		}
	});
});
