<?php

function randomAlphaNum($length){
    $rangeMin = pow(36, $length-1); //smallest number to give length digits in base 36
    $rangeMax = pow(36, $length)-1; //largest number to give length digits in base 36
    $base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number
    $newRand = base_convert($base10Rand, 10, 36); //convert it
   
    return $newRand; //spit it out
} 

$MY_PASS = "password";

$tpl = <<<EOS
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Fake GnuDip update</title>
		<meta name="retc" content="%s">
		%s
	</head>
	<body>
	<center><h2>GnuDIP Update Server</h2></center>
	</body>
</html>
EOS;

//if ($_SERVER["HTTP_USER_AGENT"] == "HUAWEI HG520v")
{
    $kv = new SaeKV();

    $kv->init();

    if (!array_key_exists('reqc', $_REQUEST))
    {   // First time
        $salt = randomAlphaNum(10);
        $time = time();
        $sign = md5($salt . $time);

        echo <<<EOS
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>Fake GnuDip update</title>
        <meta name="salt" content="$salt">
        <meta name="time" content="$time">
        <meta name="sign" content="$sign">
    </head>
    <body>
    <center><h2>GnuDIP Update Server</h2></center>
    </body>
</html>
EOS;
    } elseif ($_REQUEST['reqc'] == '0')
    {
        $salt = $_REQUEST["salt"];
        $time = $_REQUEST["time"];
        $sign = $_REQUEST["sign"];
        $user = $_REQUEST["user"];
        $pass = $_REQUEST["pass"];
        $domn = $_REQUEST["domn"];
        $addr = $_REQUEST["addr"];

        if (!$addr)
            $addr = $_SERVER['REMOTE_ADDR'];

        if ($sign != md5($salt . $time))
        {
            echo sprintf($tpl, "1", "");
        } elseif ($pass != md5(md5($MY_PASS) . "." . $salt))
        {
            echo sprintf($tpl, "1", "");
        } else
        {
            $last_ip = $kv->get("last-ip");
            if ($last_ip != $addr)
            {
                $services = array();

                array_push(
                    $services, 
                    array(
                        url => 'http://members.3322.org/dyndns/update?system=dyndns&hostname=%1$s&myip=%2$s&wildcard=ON',
                        user => "user", pass => "password", hostname => "name.3322.org"));
                array_push(
                    $services, 
                    array(
                        url => 'http://members.dyndns.org/nic/update?hostname=%1$s&myip=%2$s',
                        user => "user", pass => "password", hostname => "name.dyndns-at-home.com"));
                array_push(
                    $services, 
                    array(
                        url => 'http://members.dyndns.org/nic/update?hostname=%1$s&myip=%2$s',
                        user => "user", pass => "password", hostname => "name.dyndns.info"));

                array_push(
                    $services, 
                    array(
                        url => 'http://freedns.afraid.org/dynamic/update.php?%1$s',
                        hostname => 'hash_key'));

                foreach ($services as $service)
                {
                    $fetch = new SaeFetchurl();
                    if (array_key_exists('user', $service))
                    {
                        $fetch->setHttpAuth($service['user'], $service['pass']);
                        $fetch->fetch(
                            sprintf($service['url'], $service['hostname'], $addr),
                            array('useragent'=>'HUAWEI HG520v'));
                    }
                }

                $kv->set("last-ip", $addr);
                $kv->set("last-update", date("c"));
            }

            echo sprintf($tpl, "0", "");
        }
    } elseif ($reqc == 1)
    {
        echo sprintf($tpl, "2", "");
    } elseif ($reqc == 2)
    {
        $kv = new SaeKV();

        $extra_meta_item = "";
        if ($kv->init())
        {
            $extra_meta_item = sprintf('<meta name="addr" content="%s">', htmlspecialchars($_SERVER("REMOTE_ADDR")));
        }

        echo sprintf($tpl, "0", $extra_meta_item);
    } else
    {
        echo sprintf($tpl, "1", "");
    }
}
