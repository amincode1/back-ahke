<?php

// Set the FRED API key
$api_key = "97bf744dbfcc78df2c84265c8c2bb94f";

// Set the parameters for the request
$series_id = "DEXUSEU";
$interval = 240; // 4-hour interval
$time_period = 7; // RSI period
$start_date = date("Y-m-d", strtotime("-4 hours")); // Start date is 4 hours ago
$end_date = date("Y-m-d");

// Construct the API request URL
echo $request_url = "https://api.stlouisfed.org/fred/series/observations?series_id={$series_id}&api_key={$api_key}&file_type=json&observation_start={$start_date}&observation_end={$end_date}&interval={$interval}&units=lin&transformation=rsi&time_period={$time_period}";

// Make the API request
$response = file_get_contents($request_url);

// Decode the response JSON
$data = json_decode($response, true);

// Get the last RSI value from the response
$last_rsi_value = end($data["observations"])["value"];

// Output the last RSI value
echo "The last RSI(7) value for EUR/USD on a 4-hour interval is {$last_rsi_value}";

?>

