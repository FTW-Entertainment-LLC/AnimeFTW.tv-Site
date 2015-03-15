<?php

$services['usps']['EXPRESS'] = 'Express';
//$services['usps']['PRIORITY'] = 'Priority';
//$services['usps']['PARCEL'] = 'Parcel';

$config = array(
	// Services
	'services' => $services,
	// Weight
	'weight' => 2, // Default = 1
	'weight_units' => 'lb', // lb (default), oz, gram, kg
	// Size
	'size_length' => 5, // Default = 8
	'size_width' => 6, // Default = 4
	'size_height' => 3, // Default = 2
	'size_units' => 'in', // in (default), feet, cm
	
	// To
	'to_zip' => 'T1K5A8',
	'to_state' => "Alberta", // Only Required for FedEx
	'to_country' => "CA",
);

require_once("../classes/shippingcalculator.class.php");

$ship = new ShippingCalculator($config);

$rates = $ship->calculate();

print "
Rates for sending a ".$config[weight]." ".$config[weight_units].", ".$config[size_length]." x ".$config[size_width]." x ".$config[size_height]." ".$config[size_units]." package from ".$config[from_zip]." to ".$config[to_zip].":
<xmp>";
print_r($rates);
print "</xmp>";
?>