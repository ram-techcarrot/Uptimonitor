<?php

@ini_set('display_errors', 0);
date_default_timezone_set('Asia/Dubai');

$logFile = 'website_status.log';
if (file_exists($logFile)) {
   file_put_contents($logFile, "\n ---------------" . date("Y-m-d H:i:s") ."--------------\n", FILE_APPEND);
}

$websites = [
   "arada.com" => "",
   "wellfit.me" => "",
   "manbat.ae" => "",
   "visitzad.com" => "",
   "boostjuice.ae" => "",
   "raimondi.com" => "",
   "kbw-ventures.com" => "",
   "nestcampus.com" => "",
   "yallawheels.com" => "",
   "shajar.ae" => "",
   "ngp-solutions.com" => "",
   "hungrywolves.ae" => "",
   "everwell.ae" => "",
   "aradabrand.com" => "",
   "baitelowal.com" => "",
   "artalfashions.com" => "",
   "kbw-investments.com" => ""
];

foreach ($websites as $website => $status) {
   $pingResult = pingWebsite($website);
   $websites[$website] = [
      "status" => $pingResult["status"],
      "http_code" => $pingResult["http_code"]
   ];
}

function pingWebsite($url) {
   $ch = curl_init($url);
   curl_setopt($ch, CURLOPT_NOBODY, true);
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   $response = curl_exec($ch);
   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   curl_close($ch);
   $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
   $headers = substr($response, 0, $header_size);

   if ($httpCode >= 200 && $httpCode < 400) {
      if (strpos($headers, 'Server: cloudflare') !== false) {
         return ["status" => "Down", "http_code" => $httpCode];
      } else {
         return ["status" => "Up", "http_code" => $httpCode];
      }
   }
   return ["status" => "Down", "http_code" => $httpCode];
}
$mail_html = "";

header('Content-Type: text/html; charset=utf-8');
$mail_html .= "<h4>ARADA Websites Monitoring Status</h4>
                  <table border='1'>";
$mail_html .= "<tr><th>Website</th><th>Status</th></tr>";
foreach ($websites as $website => $status) {
    $color = ($status['status'] === "Down") ? "red" : "black";
    $mail_html .= "<tr><td style='color: $color;'>$website</td>
        <td style='color: $color;'>{$status["status"]}</td></tr>";
}
$mail_html .= "</table>";
echo $mail_html .= "<p>Note: This is a System Generated Report.</p>";

if (file_exists($logFile)) {
   file_put_contents($logFile, "\n ARADA Websites Monitoring Status :" . date("Y-m-d H:i:s") ."\n", FILE_APPEND);
   file_put_contents($logFile, json_encode($websites)."\n", FILE_APPEND);
}

$cc_recipients = [
    "ram.sharma@techcarrot.ae",
    "abdul.manaf@techcarrot.ae"
];
$cc_list = implode(", ", $cc_recipients);

$headers = "Content-Type: text/html\r\n";
$headers .= "From: noreply@techcarrot.ae\r\n";
$headers .= "Cc: " . $cc_list;

if(mail("aminda.w@techcarrot.ae", "ARADA Websites Monitoring Status :" . date("Y-m-d H:i:s"), $mail_html, $headers))
{
   file_put_contents($logFile, "Email sent successfully.\n", FILE_APPEND);
} else {
   file_put_contents($logFile, "Failed to send email.\n", FILE_APPEND);
}
file_put_contents($logFile, "\n ------------------------------------------------ \n", FILE_APPEND);

?>