jQuery(document).ready(function(e) {

  //hotel days
  jQuery('.hotel-days').on("keyup change", function(e) {
	var target_id = e.target.id;
	target_id = target_id.replace('hotel_days_','');

    var days = jQuery('#hotel_days_'+target_id).val();
    var average = jQuery('#hotel_average_'+target_id).val();
    var factor = jQuery('#hotel_factor_'+target_id).val();

    jQuery.ajax({
		type: "POST",
		url: ajax_url,
		dataType:"json",
		data: {
		  action: 'hotel_cost_calculator_ajax_calculation',
		  days: days,
		  average: average,
		  factor: factor
		},
		cache: false,
		success: function(data){
		  //expenditure
		  if(data['expenditure']==''){
			jQuery('#hotel_total_expenditure_'+target_id).val('');
		  }
		  else {
			jQuery('#hotel_total_expenditure_'+target_id).val(data['expenditure']);
		  }

		  //savings
		  if(data['savings']==''){
			jQuery('#hotel_premium_average_savings_'+target_id).val('');
		  }
		  else {
			jQuery('#hotel_premium_average_savings_'+target_id).val(data['savings']);
		  }

		  //net
		  if(data['net']==''){
			jQuery('#hotel_premium_net_'+target_id).val('');
		  }
		  else {
			jQuery('#hotel_premium_net_'+target_id).val(data['net']);
		  }

		}
    });
  });

  //hotel average cost
  jQuery('.hotel-average-night').on("keyup change", function(e) {
	var target_id = e.target.id;
	target_id = target_id.replace('hotel_average_','');

    var days = jQuery('#hotel_days_'+target_id).val();
    var average = jQuery('#hotel_average_'+target_id).val();
    var factor = jQuery('#hotel_factor_'+target_id).val();

    jQuery.ajax({
		type: "POST",
		url: ajax_url,
		dataType:"json",
		data: {
		  action: 'hotel_cost_calculator_ajax_calculation',
		  days: days,
		  average: average,
		  factor: factor
		},
		cache: false,
		success: function(data){
		  //expenditure
		  if(data['expenditure']==''){
			jQuery('#hotel_total_expenditure_'+target_id).val('');
		  }
		  else {
			jQuery('#hotel_total_expenditure_'+target_id).val(data['expenditure']);
		  }

		  //savings
		  if(data['savings']==''){
			jQuery('#hotel_premium_average_savings_'+target_id).val('');
		  }
		  else {
			jQuery('#hotel_premium_average_savings_'+target_id).val(data['savings']);
		  }

		  //net
		  if(data['net']==''){
			jQuery('#hotel_premium_net_'+target_id).val('');
		  }
		  else {
			jQuery('#hotel_premium_net_'+target_id).val(data['net']);
		  }

		}
    });
  });

});
