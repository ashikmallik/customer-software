$number="8801724110936";
$msg="This is test sms";
$url="http://api.bulksms.icombd.com/api/v3/sendsms/plain?user=
jakasoft&password=lRKZHdmy&sender=BSD&SMSText=$msg&GSM=$number";
$c=curl_init();
curl_setopt($c,CURLOPT_RETERNTRANSFER,1);
curl_setopt($c,CURLOPT_URL,$url);
$contents=curl_exec($c);
curl_close($c);