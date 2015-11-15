<?php
    
    header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

	$state = $_GET["State"];
   	$address = $_GET["StreetAddress"];
   	$city = $_GET["Cityname"];
    $return = $_GET;
    $return["city"] = $city;
    $return["state"] = $state;
   	$WebServiceURL = "https://maps.googleapis.com/maps/api/geocode/xml?address=";
	$WebServiceURL = $WebServiceURL.$address.",".$city.",".$state."&key=AIzaSyAHWwQc2ld6PevaCp-SraoJuK4KuTV9-6Q";
    $xml = new SimpleXMLElement($WebServiceURL, 0 , TRUE);
    if($xml->status[0] == "OK")
    {
       
        $latitude  = $xml->result->geometry->location->lat;
        $longitude = $xml->result->geometry->location->lng;
        
        $Units = "us";
        if($_GET["Temperature"] == "Celcius")
        {
            $Units = "si";
        }
        $WebServiceURL = "https://api.forecast.io/forecast/cd150396febdef4e4af7502861ee73f4/".$latitude.",".$longitude."?units=".$Units."&exclude=flags";
        $file = file_get_contents($WebServiceURL, true);
        $obj = json_decode($file);
        
        $return["lat"] = $obj->{'latitude'};
        $return["long"] = $obj->{'longitude'};
        
        $return["summary"] = $obj->{'currently'}->{'summary'};

        $temperature = $obj->{'currently'}->{'temperature'};

        $temperature = intval($temperature);
        $return["temperature"] = $temperature;
        $tempUnits = "&#8451";
        if($_GET["Temperature"] == "Celcius")
        {
            $tempUnits = "&#8451";
        }
        else
        {
            $tempUnits = "&#8457";
        }
        $return["tempUnits"] = $tempUnits;
        $iconvalue = $obj->{'currently'}->{'icon'};
        $return["iconvalue"] = $iconvalue;
        $ImgStr = "<center><img src=";

        if($iconvalue == "clear-day")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/clear.png' width='150px' height='150px' alt ='clear' title = 'clear'";

        if($iconvalue == "clear-night")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/clear_night.png' width='150px' height='150px' alt ='clear night' title = 'clear night'";

        if($iconvalue == "rain")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/rain.png' width='150px' height='150px' alt ='rain' title = 'rain'";

        if($iconvalue == "snow")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/snow.png' width='150px' height='150px' alt ='snow' title = 'snow'";

        if($iconvalue == "sleet")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/sleet.png' width='150px' height='150px' alt ='cloudy day' title = 'cloudy day'";

        if($iconvalue == "wind")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/wind.png' width='150px' height='150px' alt ='wind' title = 'wind'";

        if($iconvalue == "fog")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/fog.png' width='150px' height='150px' alt ='fog' title = 'fog'";

        if($iconvalue == "cloudy")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/cloudy.png' width='150px' height='150px' alt ='cloudy' title = 'cloudy'";

        if($iconvalue == "partly-cloudy-day")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/cloud_day.png' width='150px' height='150px' alt ='cloudy day' title = 'cloudy day'";

        if($iconvalue == "partly-cloudy-night")
            $ImgStr=$ImgStr."'http://cs-server.usc.edu:45678/hw/hw8/images/cloud_night.png' width='150px' height='150px' alt ='cloudy night' title = 'cloudy night'";
        
        $return["iconfb"] = $iconvalue;
        $ImgStr=$ImgStr."/></center>";
        $return["icon"] = $ImgStr;
        $precipitation = $obj->{'currently'}->{'precipIntensity'};
        $precipitationval;
        if($precipitation >= 0 && $precipitation < 0.002)
            $precipitationval = "None";
        if($precipitation >= 0.002 && $precipitation < 0.017)
            $precipitationval = "Very Light";
        if($precipitation >= 0.017 && $precipitation < 0.1)
            $precipitationval = "Light";
        if($precipitation >= 0.1 && $precipitation < 0.4)
            $precipitationval = "Moderate";
        if($precipitation >= 0.4)
            $precipitationval = "Heavy";
        $return["precipitationvalue"] = $precipitationval;
        
        $currentlyobj = $obj->{'currently'};
        if (array_key_exists('precipProbability', $currentlyobj))
        {
            $precipProbability = $currentlyobj->{'precipProbability'};
            
            $precipProbability *= 100;
            $return["precipitationprob"] = $precipProbability."%";
        }
        
        if (array_key_exists('windSpeed', $currentlyobj))
        {
            $WindSpeed = $currentlyobj->{'windSpeed'};
            $WindSpeed= number_format($WindSpeed, 2, '.', '');
            
            if($_GET["Temperature"] == "Celcius")
            {
                $WindSpeed = $WindSpeed." m/s";
            }
            else
            {
                $WindSpeed = $WindSpeed." mph";
            }
            
            $return["WindSpeed"] = $WindSpeed;
        }

        if (array_key_exists('dewPoint', $currentlyobj))
        {
            $DewPoint = $obj->{'currently'}->{'dewPoint'};
            $DewPoint = number_format($DewPoint, 2, '.', '');
            if($_GET["Temperature"] == "Celcius")
            {
                $DewPoint = $DewPoint."&#8451";
            }
            else
            {
                $DewPoint = $DewPoint."&#8457";
            }
            $return["DewPoint"]=  $DewPoint;
            
        }

        if (array_key_exists('humidity', $currentlyobj))
        {
            $humidity = $obj->{'currently'}->{'humidity'};
            $humidity *= 100;
            $return["humidity"]= $humidity."%";
        }
        
        if (array_key_exists('visibility', $currentlyobj))
        {
            $visibility = $currentlyobj->{'visibility'};
            $visibility = number_format($visibility, 2, '.', '');
            if($_GET["Temperature"] == "Celcius")
            {
                $visibility = $visibility." km";
            }
            else
            {
                $visibility = $visibility." mi";
            }
            $return["visibility"] = $visibility;
        }

        $timezone = $obj->{'timezone'};
       
        date_default_timezone_set($timezone);
        $return["sunrise"] = date('h:i A',$obj->{'daily'}->{'data'}[0]->{'sunriseTime'});
        $return["sunset"] = date('h:i A',$obj->{'daily'}->{'data'}[0]->{'sunsetTime'});
        $return["maxtemp"] = intval($obj->{'daily'}->{'data'}[0]->{'temperatureMax'});
        $return["mintemp"] = intval($obj->{'daily'}->{'data'}[0]->{'temperatureMin'});
        
        for ($i = 0; $i < 24; $i++) 
        {
            $hr = "t".$i;
            $icon = "icon".$i;
            $cloudCover = "cc".$i;
            $temperature = "tmp".$i;
            $windspeed = "wind".$i;
            $humid = "humidity".$i;
            $visible = "visibility".$i;
            $press = "pressure".$i;
            $return[$hr] = date('h:i A',$obj->{'hourly'}->{'data'}[$i]->{'time'});
            $return[$icon] = $obj->{'hourly'}->{'data'}[$i]->{'icon'};
            $cloud_cover = $obj->{'hourly'}->{'data'}[$i]->{'cloudCover'};
            $cloud_cover *= 100;
            $return[$cloudCover] = $cloud_cover."%";
            $return[$temperature] = $obj->{'hourly'}->{'data'}[$i]->{'temperature'};
            
            $wind = $obj->{'hourly'}->{'data'}[$i]->{'windSpeed'};
            $wind= number_format($wind, 2, '.', '');
            
            if($_GET["Temperature"] == "Celcius")
            {
                $wind = $wind."m/s";
            }
            else
            {
                $wind = $wind."mph";
            }
            
            $return[$windspeed] = $wind;
            
            $humidity = $obj->{'hourly'}->{'data'}[$i]->{'humidity'};
            $humidity *= 100;
            $return[$humid] = $humidity."%";
            
            $visibility = $obj->{'hourly'}->{'data'}[$i]->{'visibility'};
            $visibility = number_format($visibility, 2, '.', '');
            if($_GET["Temperature"] == "Celcius")
            {
                $visibility = $visibility."km";
            }
            else
            {
                $visibility = $visibility."mi";
            }
            $return[$visible] = $visibility;
            
            
            $pressure = $obj->{'hourly'}->{'data'}[$i]->{'pressure'};
            if($_GET["Temperature"] == "Celcius")
            {
                $pressure = $pressure."hPa";
            }
            else
            {
                $pressure = $pressure."mb";
            }
            $return[$press] = $pressure;
        }
        
        
        for ($i = 1; $i <= 7; $i++) 
        {
            $day = "nextday".$i;
            $monthdate = "nextdaydatemonth".$i;
            $icon = "nextdayicon".$i;
            $mintmp = "mintmp".$i;
            $mintmpvalue = "mintmpvalue".$i;
            $maxtmp = "maxtmp".$i;
            $maxtmpvalue = "maxtmpvalue".$i;
            $return[$day] = date('l',$obj->{'daily'}->{'data'}[$i]->{'time'});
            $monthanddate = date('M d',$obj->{'daily'}->{'data'}[$i]->{'time'});
            $return[$monthdate] = $monthanddate;
            $return[$icon] = $obj->{'daily'}->{'data'}[$i]->{'icon'};
            $mintempvalue = intval($obj->{'daily'}->{'data'}[$i]->{'temperatureMin'});
            $maxtempvalue = intval($obj->{'daily'}->{'data'}[$i]->{'temperatureMax'});
            
            $mintempvalue = $mintempvalue."&deg";
            $maxtempvalue = $maxtempvalue."&deg";
            
            
            $return[$mintmpvalue] = $mintempvalue; 
            $return[$maxtmpvalue] = $maxtempvalue; 
            
            $modallabel = "modallabel".$i;
            
            $modallabeldata = "Weather in ".$city." on ".$monthanddate;
            $return[$modallabel] = $modallabeldata;
            $nextdaymodal = "nextdaymodal".$i;
            $nextdaymodalsummary = "nextdaymodalsummary".$i;
            $nextdaymodaldata = date('l',$obj->{'daily'}->{'data'}[$i]->{'time'}).":";
            $currentlyobj =  $obj->{'daily'}->{'data'}[$i];
            $nextdaymodaldatasummarydata = "N/A";
            if (array_key_exists('summary', $currentlyobj))
            {
                $nextdaymodaldatasummarydata = $obj->{'daily'}->{'data'}[$i]->{'summary'};
            }
            else
            {
               $nextdaymodaldatasummarydata = "N/A";
            }
            $return[$nextdaymodal] = $nextdaymodaldata;
            $return[$nextdaymodalsummary] = $nextdaymodaldatasummarydata;
            $windspeedmodal = "windspeeddaily".$i;
            $windspeedmodal = "windspeeddaily".$i;
            $humidmodal = "humiditydaily".$i;
            $visiblemodal = "visibilitydaily".$i;
            $pressmodal = "pressuredaily".$i;
            $windspeeddaily = $obj->{'daily'}->{'data'}[$i]->{'windSpeed'};
            $windspeeddaily= number_format($windspeeddaily, 2, '.', '');
            
            if($_GET["Temperature"] == "Celcius")
            {
                $windspeeddaily = $windspeeddaily."m/s";
            }
            else
            {
                $windspeeddaily =$windspeeddaily."mph";
            }
            
            $return[$windspeedmodal] = $windspeeddaily;
            
            $humiditydaily = $obj->{'daily'}->{'data'}[$i]->{'humidity'};
            $humiditydaily *= 100;
            $humiditydaily = $humiditydaily."%";
            $return[$humidmodal] = $humiditydaily;
            
            $currentlyobj =  $obj->{'daily'}->{'data'}[$i];
            if (array_key_exists('visibility', $currentlyobj))
            {
                $visibilitydaily = $obj->{'daily'}->{'data'}[$i]->{'visibility'};
                $visibilitydaily = number_format($visibilitydaily, 2, '.', '');
                if($_GET["Temperature"] == "Celcius")
                {
                    $visibilitydaily = $visibilitydaily."km";
                }
                else
                {
                    $visibilitydaily = $visibilitydaily."mi";
                }
            }
            else
            {
                $visibilitydaily = "N/A";
            }
            $return[$visiblemodal] = $visibilitydaily;
            
            
            $pressuredaily = $obj->{'daily'}->{'data'}[$i]->{'pressure'};
            if($_GET["Temperature"] == "Celcius")
            {
                $pressuredaily = $pressuredaily."hPa";
            }
            else
            {
                $pressuredaily = $pressuredaily."mb";
            }
            $return[$pressmodal] = $pressuredaily;
            $sunrisemodal = "sunrisedaily".$i;
            $sunsetmodal = "sunsetdaily".$i;
            $sunrisedaily = date('h:i A',$obj->{'daily'}->{'data'}[$i]->{'sunriseTime'});
            $sunsetdaily = date('h:i A',$obj->{'daily'}->{'data'}[$i]->{'sunsetTime'});
            $return[$sunrisemodal] = $sunrisedaily;
            $return[$sunsetmodal] = $sunsetdaily;
            
        }
        
        
        $return["json"] = json_encode($return);
        echo json_encode($return);
        
    }  

?>
