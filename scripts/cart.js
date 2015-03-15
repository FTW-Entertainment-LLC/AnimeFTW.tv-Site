$(function() {
	$("#cart tr .remove input").click(function() {
		var orderCode = $(this).val();
		$.ajax({
			type: "GET",
			url: "scripts.php?view=cart",
			data: "remove[]=" + orderCode,
			success: function() {
				$("#cart tr .remove input[value=" + orderCode + "]").parent().parent().fadeOut(500, function() {
					$(this).remove();
					calcPrice();
				});
			},
			error: function() {
				window.location("scripts.php?view=cart&remove[]="+orderCode);
			}
		});
	});
	
	$("#cart tr .quantity input").change(function() {
		var orderCode = $(this).attr("name").slice(9, -1);
		var quantity = $(this).val();
		$.ajax({
			type: "GET",
			url: "scripts.php?view=cart",
			data: "quantity[" + orderCode + "]=" + quantity,
			success: function() {
				var startColor = $("#cart tr .quantity input[name*=" + orderCode + "]").parent().parent().hasClass("odd") ? "#eee" : "#fff";
				$("#cart tr .quantity input[name*=" + orderCode + "]").parent().parent().find("td").animate({ backgroundColor: "#ff8" }, 100).animate({ backgroundColor: startColor }, 800);
				calcPrice();
			},
			error: function() {
				window.location("scripts.php?view=cart&quantity[" + orderCode + "]=" + quantity);
			}
		});
	});
});

function calcPrice() {
	var totalPrice = 0;
	$("#cart tr .quantity").parent().each(function() {
		var quantity = $(".quantity input", this).val();
		var unitPrice = $(".unit_price", this).text().slice(1);
		var extendedPrice = quantity*unitPrice;
		totalPrice += extendedPrice;
		
		$(".extended_price", this).html("$" + extendedPrice);
		$("#total_price").html("$"+totalPrice);
	});
	if ( totalPrice == 0 ) {
		$("#cart").parent().replaceWith("<p class='center'>You have no items in your cart.</p>");
	}
}