

function generatevoucherCode() 
{
	$("#btnSubmit").on("click", function() {
		var $this 		    = $(this); //submit button selector using ID
        var $caption        = $this.html();// We store the html content of the submit button
        var form 			= "#generatevoucherCodeForm"; //defined the #generatevoucherCodeForm ID
        var formData        = $(form).serializeArray(); //serialize the form into array
        var route 			= $(form).attr('action'); //get the route using attribute action

        
    	$.ajax({
	        type: "POST", 
	        url: route,
	        data: formData, 
	        beforeSend: function () {
	            $this.attr('disabled', true).html("Processing...");
	        },
	        success: function (response) {
	           
	           $(form).find("[name='voucher-code']").val(response);
	        },
	        complete: function() {
	        	$this.attr('disabled', false).html($caption);
	        },
	        error: function (XMLHttpRequest, textStatus, errorThrown) {
	        	
	        }
	    });
	});
}


function usevoucherCode() 
{
	$("#btnUsevoucherCode").on("click", function() {
		var $this 		    = $(this); //submit button selector using ID
        var $caption        = $this.html();// We store the html content of the submit button
        var form 			= "#usevoucherCodeForm"; //defined form ID
        var formData        = $(form).serializeArray(); //serialize the form into array
        var route 			= $(form).attr('action'); //get the route using attribute action

        // Ajax config
    	$.ajax({
	        type: "POST", //we are using POST method to submit the data to the server side
	        url: route, // get the route value
	        data: formData, // our serialized array data for server side
	        beforeSend: function () {//We add this before send to disable the button once we submit it so that we prevent the multiple click
	            $this.attr('disabled', true).html("Processing...");
	        },
	        success: function (response) {//once the request successfully process to the server side it will return result here
	           response = JSON.parse(response);

	        	// Check if there is no validation error
	        	if(!response.hasOwnProperty('has_error')) {

	        		// We will display the result using alert
		            Swal.fire({
					  icon: 'success',
					  title: 'Success.',
					  text: response.response
					});

		            // Reset form
		            resetForm(form);
	        	} else {
	        		// We will display the result using alert
		            validationForm(form, response.errors);
	        	}
	        },
	        complete: function() {
	        	$this.attr('disabled', false).html($caption);
	        },
	        error: function (XMLHttpRequest, textStatus, errorThrown) {
	        	// You can put something here if there is an error from submitted request
	        }
	    });
	});
}

/**
 * A validation form function that will parse the json array and display to each fields
 *
 * @param {string} selector - The form selector
 * @param {json} errors - The json array response from the server form validation
 * @return {any}
 */
function validationForm(selector, errors) 
{
	// Loop the form errors
	$.each(errors, function(fieldName, fieldErrors) 
	{
		$.each(fieldErrors, function(errorType, errorValue) {

			var fieldSelector = selector + " [name='"+fieldName+"']";

			// Check if the ".form-group" class has still ".error" class
			// Then remove the ".error-message" element
			// Then rmove the ".error" class at ".form-group" class
			// To prevent element error duplication
			if($(fieldSelector).parents(".form-group").hasClass("error")) {
				$(fieldSelector).parents(".form-group").find(".error-message").remove();
				$(fieldSelector).parents(".form-group").removeClass("error");
			}

			// Insert error message after the textbox element 
			// Then add class ".error" to ".form-group" class for style needed
			$("<p class='error-message'>"+errorValue+"</p>")
				.insertAfter(fieldSelector)
				.parents(".form-group").addClass('error');

			// Remove error message on keyup by the textbox
			$(fieldSelector).on("keyup", function() {
				$(fieldSelector).parents(".form-group").find(".error-message").remove();
				$(fieldSelector).parents(".form-group").removeClass("error");
			});
		});
	});
}

function resetForm(selector) 
{
	$(selector)[0].reset();
}


/**
 * Ajax loader function
 *
 * @param {string} selector - The trigger element
 * @param {string} action - The action (show|hide) you want to apply of this function
 * @return {any}
 */
function ajaxLoader(selector, action) 
{
	var $class = "ajax-loader";

	$html = '<div class="'+$class+'"><div></div><div></div><div></div><div></div></div>';

	if(action == "show") {
		$($html).insertBefore(selector);
	} else if(action == "hide") {
		$("."+$class).hide();
	}
	
}


$(document).ready(function() {

	// Generate voucher code via ajax
	generatevoucherCode();


	// Use voucher code
	usevoucherCode();

});