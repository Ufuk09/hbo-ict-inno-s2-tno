(function( $ ) {
	$( window ).load(function() {
		$(".wpcf7 #essif-lab").click(function (e) {
			e.preventDefault();
			sendMockCall();
		});

		function sendMockCall() {
			$.ajax({
				type: 'GET',
				url: 'http://localhost:8000/api/credentialverifyrequest/1',
				success: function (data) {
					if (data != null) {
						console.log(data);
						$('input[name=postalCode]').val(data["credentialData"]["data"]["postcalCode"]);
						$('input[name=streetAddress]').val(data["credentialData"]["data"]["streetAddress"]);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("Status: " + textStatus + errorThrown);
				}
			});
		}
	});
})( jQuery );
