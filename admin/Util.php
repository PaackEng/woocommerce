<?php

    function getHoursOptions(){
        return array(
            "now"=>"Próximas 2 horas.",
            "four"=>"16:00 - 17:00",
            "five"=>"17:00 - 18:00"
        );
    }
    function getHourSelected($selected){
        return getHoursOptions()[$selected];
    }
    function getHours($selected){
        $options = array(
            "four"=>16,
            "five"=>17
        );
        return $options[$selected];
    }
    function getUTCDate(){
		$date = new Datetime();
		$date_format= $date->format("Y-m-d H:i");
        $utc_date = DateTime::createFromFormat(
			'Y-m-d G:i',
			$date_format,
			new DateTimeZone('UTC')
        );
        return $utc_date;
    }
    function formatDate($date){
		$date_format = $date->format("Y-m-d H:i:s");
        $date_format = str_replace(' ','T',$date_format).".000Z";
        return $date_format;
    }
 function addDay($date,$day){
        $date->add(new DateInterval('P'.$day.'D'));
        return $date;
    }
    function addHour($date,$hour){
        $date->add(new DateInterval('PT'.$hour.'H'));
        return $date;
    }
?>