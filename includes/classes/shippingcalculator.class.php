<?php
/****************************************************************\
## FileName: shipping.class.php									 
## Author: Brad Riemann										 
## Usage: Shipping Calculation class, based on a publicly available class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class ShippingCalculator  {
	// Defaults
	var $weight = 1;
	var $weight_unit = "lb";
	var $size_length = 4;
	var $size_width = 8;
	var $size_height = 2;
	var $size_unit = "in";
	var $debug = true; // Change to true to see XML sent and recieved 
	
	// Batch (get all rates in one go, saves lots of time)
	var $batch_ups = false; // Currently Unavailable
	var $batch_usps = true; 
	var $batch_fedex = false; // Currently Unavailable
	
	// Config (you can either set these here or send them in a config array when creating an instance of the class)
	var $services;
	var $from_zip = '60123';
	var $from_state = 'IL';
	var $from_country = 'US';
	var $to_zip;
	var $to_stat;
	var $to_country;
	var $ups_access = '5CB98A8FC7021535';
	var $ups_user = 'ftwentertainment';
	var $ups_pass = 'Ftw3nt3rtainm3ntUPS';
	var $ups_account = '8V7R15';
	var $usps_user = '089FTWEN7987';
	var $usps_pass = '482HB82ND597';
	var $fedex_account = 'ftwentertainment';
	var $fedex_meter = 'Ftw3nt3r123!!';
	
	// Results
	var $rates;
	
	// Setup Class with Config Options
	public function __construct($config)
	{
		if($config) 
		{
			foreach($config as $k => $v) $this->$k = $v;
		}
	}
	
	// Calculate
	public function calculate($company = NULL,$code = NULL)
	{
		$this->rates = NULL;
		$services = $this->services;
		if($company and $code) $services[$company][$code] = 1;
		foreach($services as $company => $codes) {
			foreach($codes as $code => $name) {
				switch($company) {
					case "ups": 
						/*if($this->batch_ups == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/ $this->rates[$company][$code] = $this->calculate_ups($code);
						break;
					case "usps":
						if($this->batch_usps == true) $batch[] = $code;
						else $this->rates[$company][$code] = $this->calculate_usps($code);
						break;
					case "fedex": 
						/*if($this->batch_fedex == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/ $this->rates[$company][$code] = $this->calculate_fedex($code);
						break;
				}
			}
			// Batch Rates
			//if($company == "ups" and $this->batch_ups == true and count($batch) > 0) $this->rates[$company] = $this->calculate_ups($batch);
			if($company == "usps" and $this->batch_usps == true and count($batch) > 0) $this->rates[$company] = $this->calculate_usps($batch);
			//if($company == "fedex" and $this->batch_fedex == true and count($batch) > 0) $this->rates[$company] = $this->calculate_fedex($batch);
		}
		
		return $this->rates;
	}
	
	// Calculate UPS
	private function calculate_ups($code) 
	{
		$url = "https://www.ups.com/ups.app/xml/Rate";
    	$data = '<?xml version="1.0"?>  
<AccessRequest xml:lang="en-US">  
	<AccessLicenseNumber>'.$this->ups_access.'</AccessLicenseNumber>  
	<UserId>'.$this->ups_user.'</UserId>  
	<Password>'.$this->ups_pass.'</Password>  
</AccessRequest>  
<?xml version="1.0"?>  
<RatingServiceSelectionRequest xml:lang="en-US">  
	<Request>  
		<TransactionReference>  
			<CustomerContext>Bare Bones Rate Request</CustomerContext>  
			<XpciVersion>1.0001</XpciVersion>  
		</TransactionReference>  
		<RequestAction>Rate</RequestAction>  
		<RequestOption>Rate</RequestOption>  
	</Request>  
	<PickupType>  
		<Code>01</Code>  
	</PickupType>  
	<Shipment>  
		<Shipper>  
			<Address>  
				<PostalCode>'.$this->from_zip.'</PostalCode>  
				<CountryCode>'.$this->from_country.'</CountryCode>  
			</Address>  
		<ShipperNumber>'.$this->ups_account.'</ShipperNumber>  
		</Shipper>  
		<ShipTo>  
			<Address>  
				<PostalCode>'.$this->to_zip.'</PostalCode>  
				<CountryCode>'.$this->to_country.'</CountryCode>  
			<ResidentialAddressIndicator/>  
			</Address>  
		</ShipTo>  
		<ShipFrom>  
			<Address>  
				<PostalCode>'.$this->from_zip.'</PostalCode>  
				<CountryCode>'.$this->from_country.'</CountryCode>  
			</Address>  
		</ShipFrom>  
		<Service>  
			<Code>'.$code.'</Code>  
		</Service>  
		<Package>  
			<PackagingType>  
				<Code>02</Code>  
			</PackagingType>  
			<Dimensions>  
				<UnitOfMeasurement>  
					<Code>IN</Code>  
				</UnitOfMeasurement>  
				<Length>'.($this->size_unit != "in" ? $this->convert_sze($this->size_length,$this->size_unit,"in") : $this->size_length).'</Length>  
				<Width>'.($this->size_unit != "in" ? $this->convert_sze($this->size_width,$this->size_unit,"in") : $this->size_width).'</Width>  
				<Height>'.($this->size_unit != "in" ? $this->convert_sze($this->size_height,$this->size_unit,"in") : $this->size_height).'</Height>  
			</Dimensions>  
			<PackageWeight>  
				<UnitOfMeasurement>  
					<Code>LBS</Code>  
				</UnitOfMeasurement>  
				<Weight>'.($this->weight_unit != "lb" ? $this->convert_weight($this->weight,$this->weight_unit,"lb") : $this->weight).'</Weight>  
			</PackageWeight>  
		</Package>  
	</Shipment>  
</RatingServiceSelectionRequest>'; 
		
		// Curl
		$results = $this->curl($url,$data);
		
		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}
		
		// Match Rate
		preg_match('/<MonetaryValue>(.*?)<\/MonetaryValue>/',$results,$rate);
		
		return $rate[1];
	}
	
	// Calculate USPS
	private function calculate_usps($code) {
		// Weight (in lbs)
		if($this->weight_unit != 'lb') $weight = $this->convert_weight($weight,$this->weight_unit,'lb');
		else $weight = $this->weight;
		// Split into Lbs and Ozs
		$lbs = floor($weight);
		$ozs = ($weight - $lbs)  * 16;
		if($lbs == 0 and $ozs < 1) $ozs = 1;
		// Code(s)
		$array = true;
		if(!is_array($code)) {
			$array = false;
			$code = array($code);
		}
		
		$url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
		$data = $url."?API=RateV4&XML=<RateV4Request%20USERID=\"" . urlencode($this->usps_user) . "\"%20PASSWORD=\"" . urlencode($this->usps_pass) . "\"><Revision/>";
		//$data = $url.'?API=RateV4&XML=<RateV4Request USERID="'.$this->usps_user.'" PASSWORD="">';
        foreach($code as $x => $c) $data .= "<Package%20ID=\"" . $x . "\"><Service>" . urlencode($c) . "</Service><ZipOrigination>" . urlencode($this->from_zip) . "</ZipOrigination><ZipDestination>" . urlencode($this->to_zip) . "</ZipDestination><Pounds>" . urlencode($lbs) . "</Pounds><Ounces>" . $ozs . "</Ounces><Container/><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package>";
        $data .= '</RateV4Request>';

// $data .= '<Package ID="'.$x.'"><Service>'.$c.'</Service><ZipOrigination>'.$this->from_zip.'</ZipOrigination><ZipDestination>'.$this->to_zip.'</ZipDestination><Pounds>'.$lbs.'</Pounds><Ounces>'.$ozs.'</Ounces><Container/><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package>';
		
		// Curl
		$results = $this->curl($url,$data);
		
		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}
		
		// Match Rate(s)
		preg_match_all('/<Package ID="([0-9]{1,3})">(.+?)<\/Package>/',$results,$packages);
		foreach($packages[1] as $x => $package) {
			preg_match('/<Rate>(.+?)<\/Rate>/',$packages[2][$x],$rate);
			$rates[$code[$package]] = $rate[1];
		}
		if($array == true) return $rates;
		else return $rate[1];
	}
	
	// Calculate FedEX
	private function calculate_fedex($code) {
		$url = "https://gatewaybeta.fedex.com/GatewayDC";
		$data = '<?xml version="1.0" encoding="UTF-8" ?>
<FDXRateRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXRateRequest.xsd">
	<RequestHeader>
		<CustomerTransactionIdentifier>Express Rate</CustomerTransactionIdentifier>
		<AccountNumber>'.$this->fedex_account.'</AccountNumber>
		<MeterNumber>'.$this->fedex_meter.'</MeterNumber>
		<CarrierCode>'.(in_array($code,array('FEDEXGROUND','GROUNDHOMEDELIVERY')) ? 'FDXG' : 'FDXE').'</CarrierCode>
	</RequestHeader>
	<DropoffType>REGULARPICKUP</DropoffType>
	<Service>'.$code.'</Service>
	<Packaging>YOURPACKAGING</Packaging>
	<WeightUnits>LBS</WeightUnits>
	<Weight>'.number_format(($this->weight_unit != 'lb' ? convert_weight($this->weight,$this->weight_unit,'lb') : $this->weight), 1, '.', '').'</Weight>
	<OriginAddress>
		<StateOrProvinceCode>'.$this->from_state.'</StateOrProvinceCode>
		<PostalCode>'.$this->from_zip.'</PostalCode>
		<CountryCode>'.$this->from_country.'</CountryCode>
	</OriginAddress>
	<DestinationAddress>
		<StateOrProvinceCode>'.$this->to_state.'</StateOrProvinceCode>
		<PostalCode>'.$this->to_zip.'</PostalCode>
		<CountryCode>'.$this->to_country.'</CountryCode>
	</DestinationAddress>
	<Payment>
		<PayorType>SENDER</PayorType>
	</Payment>
	<PackageCount>1</PackageCount>
</FDXRateRequest>';
		
		// Curl
		$results = $this->curl($url,$data);
		
		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}
	
		// Match Rate
		preg_match('/<NetCharge>(.*?)<\/NetCharge>/',$results,$rate);
		
		return $rate[1];
	}
	
	// Curl
	private function curl($url,$data = NULL) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if($data) {
			curl_setopt($ch, CURLOPT_POST,1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		}  
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$contents = curl_exec ($ch);
		
		return $contents;
		
		curl_close ($ch);
	}
	
	// Convert Weight
	private function convert_weight($weight,$old_unit,$new_unit) 
	{
		$units['oz'] = 1;
		$units['lb'] = 0.0625;
		$units['gram'] = 28.3495231;
		$units['kg'] = 0.0283495231;
		
		// Convert to Ounces (if not already)
		if($old_unit != "oz") $weight = $weight / $units[$old_unit];
		
		// Convert to New Unit
		$weight = $weight * $units[$new_unit];
		
		// Minimum Weight
		if($weight < .1) $weight = .1;
		
		// Return New Weight
		return round($weight,2);
	}
	
	// Convert Size
	private function convert_size($size,$old_unit,$new_unit) 
	{
		$units['in'] = 1;
		$units['cm'] = 2.54;
		$units['feet'] = 0.083333;
		
		// Convert to Inches (if not already)
		if($old_unit != "in") $size = $size / $units[$old_unit];
		
		// Convert to New Unit
		$size = $size * $units[$new_unit];
		
		// Minimum Size
		if($size < .1) $size = .1;
		
		// Return New Size
		return round($size,2);
	}
	
	// Set Value
	public function set_value($k,$v) 
	{
		$this->$k = $v;
	}
}
?>