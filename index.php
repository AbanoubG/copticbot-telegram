<?php

require 'vendor/autoload.php';

use meysampg\intldate\IntlDateTrait;

class Cal
{
    use IntlDateTrait;    
    private function getMonth($num)
    {
        switch ($num) {
            case '1':
                return 'Thoout';
                break;
            case '2':
                return 'Paope';
                break;
            case '3':
                return 'Hathor';
                break;
            case '4':
                return 'Koiahk';
                break;
            case '5':
                return 'Tobe';
                break;
            case '6':
                return 'Meshir';
                break;
            case '7':
                return 'Paremhotep';
                break;
            case '8':
                return 'Parmoute';
                break;
            case '9':
                return 'Pashons';
                break;
            case '10':
                return 'Paone';
                break;
            case '11':
                return 'Epep';
                break;
            case '12':
                return 'Mesore';
                break;
            case '13':
                return 'Nesi';
                break;
            default:
                return 'error';
                break;
        }
    }
    public function getDate()
    {
        $rawdate = date('Y-m-d');
        $date = explode('-', $rawdate);
        $result = $this->fromGregorian([$date[0], $date[1], $date[2]])->toCoptic()->asDateTime();
        $dateres = substr($result, 0, -10);
        $datecomp = explode('/', $dateres);
        $m = $this->getMonth($datecomp[1]);
        $d = $datecomp[2];
        $y = $datecomp[0];
        return "$m $d, $y";
    }
    public function getCopticMonth()
    {
        $rawdate = date('Y-m-d');
        $date = explode('-', $rawdate);
        $result = $this->fromGregorian([$date[0], $date[1], $date[2]])->toCoptic()->asDateTime();
        $dateres = substr($result, 0, -10);
        $datecomp = explode('/', $dateres);
        $m = $this->getMonth($datecomp[1]);        
        return "$m";
    }
    public function getCopticDay()
    {
        $rawdate = date('Y-m-d');
        $date = explode('-', $rawdate);
        $result = $this->fromGregorian([$date[0], $date[1], $date[2]])->toCoptic()->asDateTime();
        $dateres = substr($result, 0, -10);
        $datecomp = explode('/', $dateres);        
        $d = $datecomp[2]-1;        
        return "$d";
    }
}

//$t = new Telegram('757146827:AAG2Q71HrkB9eG9c2-5V960bvJLAmN479g0');
$t = new Telegram('--------------------------add your bot token here--------------------------------'); 

$chat_id = $t->ChatID();
$result = $t->getData();
$text = $result['message']['text'];
$calendar = new Cal;
$content = array('chat_id' => $chat_id);
 
$year = date("Y");
$month = date("n");
$ex_month = date("F");
$day = date("j");
$ex_day = date("d");
$data = file_get_contents("year/" . $ex_month . "/saints.json");
$json = json_decode($data);
$n = $day - 1;
$i = 0;
$saint = ''; 

do {
    if (
        isset($json->days[$n]->celebrations[$i]) &&
        $json->days[$n]->celebrations[$i] != ""
    ) {
        $saint .= "- " . $json->days[$n]->celebrations[$i]->title . "\n";
        $i++;
    } else {
        break;
    }
} while (1);

function doAbout($ex_month,$day,$copticdate)
{
    $data = file_get_contents("coptic_year/" . $ex_month . ".json");
    $json = json_decode($data);
    $n = $day ;
    $i = 0;
    $saint = '';   
    $saint .="Today is : ".$copticdate."\n\n";
    do {
        if (
            isset($json->days[$n]->celebrations[$i]) &&
            $json->days[$n]->celebrations[$i] != ""
        ) {
            $saint .= "(".($i+1).") -" . $json->days[$n]->celebrations[$i]->title . "\n";
            $i++;
        } else {
            break;
        }
    } while (1);
    return ($saint);
}

if ($text) {
    switch ($text) {
        case '/start':
            $content['text'] = "Welcome to CopticBot. You can use /date to get the Coptic date or /saint to get the saint of the day. ";
            $t->sendMessage($content);
            break;
        case '/date':
            $date = $calendar->getDate();
            $content['text'] = "Today is $date";
            $t->sendMessage($content);
            break;
        case '/saint':
            $content['text'] = "$saint";
            $t->sendMessage($content);
            break;        
        case '/about':   
                $copticdate = $calendar->getDate();         
                $date = $calendar->getCopticMonth();
                $day = $calendar->getCopticDay();
                $content['text'] = doAbout($date,$day,$copticdate);
                $t->sendMessage($content);
                break;
        case '/month':            
            $date = $calendar->getCopticMonth();
            $content['text'] = "Current Coptic Month is $date";
            $t->sendMessage($content);
            break;
        default:
            $content['text'] = "Wrong command, type /date to know the coptic date";
            $t->sendMessage($content);
            break;
    }
}
