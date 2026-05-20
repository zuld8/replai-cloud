<?php

use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\FineTunnel;
use App\Models\Courier\CourierFineTunnel;
use App\Models\InternalSetting;
use App\Models\LiveChat;
use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

if (! function_exists('isActive')) {
    /**
     * Set the active class to the current opened menu.
     *
     * @param  string|array $route
     * @param  string       $className
     * @return string
     */
    function isActive($route, $className = 'active')
    {
        if (is_array($route)) {
            return in_array(Route::currentRouteName(), $route) ? $className : '';
        }
        if (Route::currentRouteName() == $route) {
            return $className;
        }
        if (strpos(URL::current(), $route)) {
            return $className;
        }
    }
}

if (! function_exists('time_format')) {
    /**
     * Set the active class to the current opened menu.
     *
     * @param  string|array $route
     * @param  string       $className
     * @return string
     */
    function time_format($date, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($date);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'Tahun',
            'm' => 'Bulan',
            'w' => 'Minggu',
            'd' => 'Hari',
            'h' => 'Jam',
            'i' => 'Menit',
            's' => 'Detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' lalu' : 'Baru Saja';
    }
}

if (!function_exists('tanggal_indo')) {

    function tanggal_indo($tanggal)
    {
        if ($tanggal != null) {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            $split = explode('-', $tanggal);

            return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0] . ' ';
        }
    }
}

if (!function_exists('timezone')) {

    function timezone()
    {
        $timezones = [
            'Africa/Abidjan' => 'Africa/Abidjan',
            'Africa/Accra' => 'Africa/Accra',
            'Africa/Addis_Ababa' => 'Africa/Addis_Ababa',
            'Africa/Algiers' => 'Africa/Algiers',
            'Africa/Asmara' => 'Africa/Asmara',
            'Africa/Bamako' => 'Africa/Bamako',
            'Africa/Bangui' => 'Africa/Bangui',
            'Africa/Banjul' => 'Africa/Banjul',
            'Africa/Bissau' => 'Africa/Bissau',
            'Africa/Blantyre' => 'Africa/Blantyre',
            'Africa/Brazzaville' => 'Africa/Brazzaville',
            'Africa/Bujumbura' => 'Africa/Bujumbura',
            'Africa/Cairo' => 'Africa/Cairo',
            'Africa/Casablanca' => 'Africa/Casablanca',
            'Africa/Ceuta' => 'Africa/Ceuta',
            'Africa/Conakry' => 'Africa/Conakry',
            'Africa/Dakar' => 'Africa/Dakar',
            'Africa/Dar_es_Salaam' => 'Africa/Dar_es_Salaam',
            'Africa/Djibouti' => 'Africa/Djibouti',
            'Africa/Douala' => 'Africa/Douala',
            'Africa/El_Aaiun' => 'Africa/El_Aaiun',
            'Africa/Freetown' => 'Africa/Freetown',
            'Africa/Gaborone' => 'Africa/Gaborone',
            'Africa/Harare' => 'Africa/Harare',
            'Africa/Johannesburg' => 'Africa/Johannesburg',
            'Africa/Kampala' => 'Africa/Kampala',
            'Africa/Khartoum' => 'Africa/Khartoum',
            'Africa/Kigali' => 'Africa/Kigali',
            'Africa/Kinshasa' => 'Africa/Kinshasa',
            'Africa/Lagos' => 'Africa/Lagos',
            'Africa/Libreville' => 'Africa/Libreville',
            'Africa/Luanda' => 'Africa/Luanda',
            'Africa/Lubumbashi' => 'Africa/Lubumbashi',
            'Africa/Lusaka' => 'Africa/Lusaka',
            'Africa/Malabo' => 'Africa/Malabo',
            'Africa/Maputo' => 'Africa/Maputo',
            'Africa/Maseru' => 'Africa/Maseru',
            'Africa/Mbabane' => 'Africa/Mbabane',
            'Africa/Mogadishu' => 'Africa/Mogadishu',
            'Africa/Monrovia' => 'Africa/Monrovia',
            'Africa/Nairobi' => 'Africa/Nairobi',
            'Africa/Ndjamena' => 'Africa/Ndjamena',
            'Africa/Niamey' => 'Africa/Niamey',
            'Africa/Nouakchott' => 'Africa/Nouakchott',
            'Africa/Ouagadougou' => 'Africa/Ouagadougou',
            'Africa/Porto-Novo' => 'Africa/Porto-Novo',
            'Africa/Sao_Tome' => 'Africa/Sao_Tome',
            'Africa/Timbuktu' => 'Africa/Timbuktu',
            'Africa/Tunis' => 'Africa/Tunis',
            'Africa/Windhoek' => 'Africa/Windhoek',
            'America/Adak' => 'America/Adak',
            'America/Anchorage' => 'America/Anchorage',
            'America/Anguilla' => 'America/Anguilla',
            'America/Antigua' => 'America/Antigua',
            'America/Araguaina' => 'America/Araguaina',
            'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires',
            'America/Argentina/Catamarca' => 'America/Argentina/Catamarca',
            'America/Argentina/ComodRivadavia' => 'America/Argentina/ComodRivadavia',
            'America/Argentina/Jujuy' => 'America/Argentina/Jujuy',
            'America/Argentina/La_Rioja' => 'America/Argentina/La_Rioja',
            'America/Argentina/Mendoza' => 'America/Argentina/Mendoza',
            'America/Argentina/Rio_Gallegos' => 'America/Argentina/Rio_Gallegos',
            'America/Argentina/Salta' => 'America/Argentina/Salta',
            'America/Argentina/San_Juan' => 'America/Argentina/San_Juan',
            'America/Argentina/San_Luis' => 'America/Argentina/San_Luis',
            'America/Argentina/Tucuman' => 'America/Argentina/Tucuman',
            'America/Argentina/Ushuaia' => 'America/Argentina/Ushuaia',
            'America/Aruba' => 'America/Aruba',
            'America/Asuncion' => 'America/Asuncion',
            'America/Atikokan' => 'America/Atikokan',
            'America/Bahia' => 'America/Bahia',
            'America/Bahia_Banderas' => 'America/Bahia_Banderas',
            'America/Barbados' => 'America/Barbados',
            'America/Belem' => 'America/Belem',
            'America/Belize' => 'America/Belize',
            'America/Benques' => 'America/Benques',
            'America/Blanc-Sablon' => 'America/Blanc-Sablon',
            'America/Boa_Vista' => 'America/Boa_Vista',
            'America/Bogota' => 'America/Bogota',
            'America/Boise' => 'America/Boise',
            'America/Cambridge_Bay' => 'America/Cambridge_Bay',
            'America/Campo_Grande' => 'America/Campo_Grande',
            'America/Cancun' => 'America/Cancun',
            'America/Caracas' => 'America/Caracas',
            'America/Cayenne' => 'America/Cayenne',
            'America/Cayman' => 'America/Cayman',
            'America/Chicago' => 'America/Chicago',
            'America/Chihuahua' => 'America/Chihuahua',
            'America/Costa_Rica' => 'America/Costa_Rica',
            'America/Cuiaba' => 'America/Cuiaba',
            'America/Curacao' => 'America/Curacao',
            'America/Danmarkshavn' => 'America/Danmarkshavn',
            'America/Dawson' => 'America/Dawson',
            'America/Dawson_Creek' => 'America/Dawson_Creek',
            'America/Denver' => 'America/Denver',
            'America/Detroit' => 'America/Detroit',
            'America/Dominica' => 'America/Dominica',
            'America/Edmonton' => 'America/Edmonton',
            'America/Eirunepe' => 'America/Eirunepe',
            'America/El_Salvador' => 'America/El_Salvador',
            'America/Fort_Nelson' => 'America/Fort_Nelson',
            'America/Fortaleza' => 'America/Fortaleza',
            'America/Glace_Bay' => 'America/Glace_Bay',
            'America/Godthab' => 'America/Godthab',
            'America/Goose_Bay' => 'America/Goose_Bay',
            'America/Grand_Turk' => 'America/Grand_Turk',
            'America/Grenada' => 'America/Grenada',
            'America/Guadeloupe' => 'America/Guadeloupe',
            'America/Guatemala' => 'America/Guatemala',
            'America/Guayaquil' => 'America/Guayaquil',
            'America/Guyana' => 'America/Guyana',
            'America/Halifax' => 'America/Halifax',
            'America/Havana' => 'America/Havana',
            'America/Hermosillo' => 'America/Hermosillo',
            'America/Indiana/Indianapolis' => 'America/Indiana/Indianapolis',
            'America/Indiana/Knox' => 'America/Indiana/Knox',
            'America/Indiana/Marengo' => 'America/Indiana/Marengo',
            'America/Indiana/Petersburg' => 'America/Indiana/Petersburg',
            'America/Indiana/Tell_City' => 'America/Indiana/Tell_City',
            'America/Indiana/Vincennes' => 'America/Indiana/Vincennes',
            'America/Indiana/Winamac' => 'America/Indiana/Winamac',
            'America/Inuvik' => 'America/Inuvik',
            'America/Iqaluit' => 'America/Iqaluit',
            'America/Jamaica' => 'America/Jamaica',
            'America/Jujuy' => 'America/Jujuy',
            'America/Juneau' => 'America/Juneau',
            'America/Kentucky/Louisville' => 'America/Kentucky/Louisville',
            'America/Kentucky/Monticello' => 'America/Kentucky/Monticello',
            'America/Kralendijk' => 'America/Kralendijk',
            'America/La_Paz' => 'America/La_Paz',
            'America/Lima' => 'America/Lima',
            'America/Los_Angeles' => 'America/Los_Angeles',
            'America/Lower_Princes' => 'America/Lower_Princes',
            'America/Maceio' => 'America/Maceio',
            'America/Managua' => 'America/Managua',
            'America/Manaus' => 'America/Manaus',
            'America/Marigot' => 'America/Marigot',
            'America/Martinique' => 'America/Martinique',
            'America/Matamoros' => 'America/Matamoros',
            'America/Mazatlan' => 'America/Mazatlan',
            'America/Mendoza' => 'America/Mendoza',
            'America/Menominee' => 'America/Menominee',
            'America/Mexico_City' => 'America/Mexico_City',
            'America/Miquelon' => 'America/Miquelon',
            'America/Moncton' => 'America/Moncton',
            'America/Monterrey' => 'America/Monterrey',
            'America/Montevideo' => 'America/Montevideo',
            'America/Montreal' => 'America/Montreal',
            'America/Montserrat' => 'America/Montserrat',
            'America/Nassau' => 'America/Nassau',
            'America/New_York' => 'America/New_York',
            'America/Nipigon' => 'America/Nipigon',
            'America/Nome' => 'America/Nome',
            'America/Noronha' => 'America/Noronha',
            'America/North_Dakota/Beulah' => 'America/North_Dakota/Beulah',
            'America/North_Dakota/Center' => 'America/North_Dakota/Center',
            'America/North_Dakota/New_Salem' => 'America/North_Dakota/New_Salem',
            'America/Ojinaga' => 'America/Ojinaga',
            'America/Panama' => 'America/Panama',
            'America/Pangnirtung' => 'America/Pangnirtung',
            'America/Paramaribo' => 'America/Paramaribo',
            'America/Phoenix' => 'America/Phoenix',
            'America/Port-au-Prince' => 'America/Port-au-Prince',
            'America/Port_of_Spain' => 'America/Port_of_Spain',
            'America/Puerto_Rico' => 'America/Puerto_Rico',
            'America/Punta_Arenas' => 'America/Punta_Arenas',
            'America/Rainy_River' => 'America/Rainy_River',
            'America/Rankin_Inlet' => 'America/Rankin_Inlet',
            'America/Recife' => 'America/Recife',
            'America/Regina' => 'America/Regina',
            'America/Resolute' => 'America/Resolute',
            'America/Rio_Branco' => 'America/Rio_Branco',
            'America/Santa_Isabel' => 'America/Santa_Isabel',
            'America/Santarem' => 'America/Santarem',
            'America/Santiago' => 'America/Santiago',
            'America/Santo_Domingo' => 'America/Santo_Domingo',
            'America/Sao_Paulo' => 'America/Sao_Paulo',
            'America/Scoresby_Sund' => 'America/Scoresby_Sund',
            'America/Sitka' => 'America/Sitka',
            'America/St_Barthelemy' => 'America/St_Barthelemy',
            'America/St_Johns' => 'America/St_Johns',
            'America/St_Kitts' => 'America/St_Kitts',
            'America/St_Lucia' => 'America/St_Lucia',
            'America/St_Thomas' => 'America/St_Thomas',
            'America/St_Vincent' => 'America/St_Vincent',
            'America/Swift_Current' => 'America/Swift_Current',
            'America/Tegucigalpa' => 'America/Tegucigalpa',
            'America/Thule' => 'America/Thule',
            'America/Thunder_Bay' => 'America/Thunder_Bay',
            'America/Tijuana' => 'America/Tijuana',
            'America/Toronto' => 'America/Toronto',
            'America/Tortola' => 'America/Tortola',
            'America/Vancouver' => 'America/Vancouver',
            'America/Whitehorse' => 'America/Whitehorse',
            'America/Winnipeg' => 'America/Winnipeg',
            'America/Yakutat' => 'America/Yakutat',
            'America/Yellowknife' => 'America/Yellowknife',
            'Antarctica/Palmer' => 'Antarctica/Palmer',
            'Antarctica/Rothera' => 'Antarctica/Rothera',
            'Antarctica/Scott' => 'Antarctica/Scott',
            'Antarctica/Syowa' => 'Antarctica/Syowa',
            'Antarctica/Vostok' => 'Antarctica/Vostok',
            'Asia/Almaty' => 'Asia/Almaty',
            'Asia/Amman' => 'Asia/Amman',
            'Asia/Anadyr' => 'Asia/Anadyr',
            'Asia/Aqtau' => 'Asia/Aqtau',
            'Asia/Aqtobe' => 'Asia/Aqtobe',
            'Asia/Ashgabat' => 'Asia/Ashgabat',
            'Asia/Baghdad' => 'Asia/Baghdad',
            'Asia/Baku' => 'Asia/Baku',
            'Asia/Bangkok' => 'Asia/Bangkok',
            'Asia/Barnaul' => 'Asia/Barnaul',
            'Asia/Beirut' => 'Asia/Beirut',
            'Asia/Bishkek' => 'Asia/Bishkek',
            'Asia/Brunei' => 'Asia/Brunei',
            'Asia/Calcutta' => 'Asia/Calcutta',
            'Asia/Choibalsan' => 'Asia/Choibalsan',
            'Asia/Chongqing' => 'Asia/Chongqing',
            'Asia/Colombo' => 'Asia/Colombo',
            'Asia/Damascus' => 'Asia/Damascus',
            'Asia/Dhaka' => 'Asia/Dhaka',
            'Asia/Dili' => 'Asia/Dili',
            'Asia/Dubai' => 'Asia/Dubai',
            'Asia/Dushanbe' => 'Asia/Dushanbe',
            'Asia/Gaza' => 'Asia/Gaza',
            'Asia/Hebron' => 'Asia/Hebron',
            'Asia/Ho_Chi_Minh' => 'Asia/Ho_Chi_Minh',
            'Asia/Hong_Kong' => 'Asia/Hong_Kong',
            'Asia/Hovd' => 'Asia/Hovd',
            'Asia/Irkutsk' => 'Asia/Irkutsk',
            'Asia/Jakarta' => 'Asia/Jakarta',
            'Asia/Jayapura' => 'Asia/Jayapura',
            'Asia/Jerusalem' => 'Asia/Jerusalem',
            'Asia/Kabul' => 'Asia/Kabul',
            'Asia/Kamchatka' => 'Asia/Kamchatka',
            'Asia/Karachi' => 'Asia/Karachi',
            'Asia/Kashgar' => 'Asia/Kashgar',
            'Asia/Kathmandu' => 'Asia/Kathmandu',
            'Asia/Kolkata' => 'Asia/Kolkata',
            'Asia/Krasnoyarsk' => 'Asia/Krasnoyarsk',
            'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur',
            'Asia/Kuching' => 'Asia/Kuching',
            'Asia/Kuwait' => 'Asia/Kuwait',
            'Asia/Macau' => 'Asia/Macau',
            'Asia/Magadan' => 'Asia/Magadan',
            'Asia/Makassar' => 'Asia/Makassar',
            'Asia/Manila' => 'Asia/Manila',
            'Asia/Muscat' => 'Asia/Muscat',
            'Asia/Nicosia' => 'Asia/Nicosia',
            'Asia/Novokuznetsk' => 'Asia/Novokuznetsk',
            'Asia/Novosibirsk' => 'Asia/Novosibirsk',
            'Asia/Omsk' => 'Asia/Omsk',
            'Asia/Oral' => 'Asia/Oral',
            'Asia/Phnom_Penh' => 'Asia/Phnom_Penh',
            'Asia/Pontianak' => 'Asia/Pontianak',
            'Asia/Pyongyang' => 'Asia/Pyongyang',
            'Asia/Qatar' => 'Asia/Qatar',
            'Asia/Qostanay' => 'Asia/Qostanay',
            'Asia/Qyzylorda' => 'Asia/Qyzylorda',
            'Asia/Riyadh' => 'Asia/Riyadh',
            'Asia/Sakhalin' => 'Asia/Sakhalin',
            'Asia/Samarkand' => 'Asia/Samarkand',
            'Asia/Seoul' => 'Asia/Seoul',
            'Asia/Shanghai' => 'Asia/Shanghai',
            'Asia/Singapore' => 'Asia/Singapore',
            'Asia/Srednekolymsk' => 'Asia/Srednekolymsk',
            'Asia/Taipei' => 'Asia/Taipei',
            'Asia/Tashkent' => 'Asia/Tashkent',
            'Asia/Tbilisi' => 'Asia/Tbilisi',
            'Asia/Tehran' => 'Asia/Tehran',
            'Asia/Tokyo' => 'Asia/Tokyo',
            'Asia/Tomsk' => 'Asia/Tomsk',
            'Asia/Ulaanbaatar' => 'Asia/Ulaanbaatar',
            'Asia/Urumqi' => 'Asia/Urumqi',
            'Asia/Ust-Nera' => 'Asia/Ust-Nera',
            'Asia/Vientiane' => 'Asia/Vientiane',
            'Asia/Vladivostok' => 'Asia/Vladivostok',
            'Asia/Yakutsk' => 'Asia/Yakutsk',
            'Asia/Yangon' => 'Asia/Yangon',
            'Asia/Yekaterinburg' => 'Asia/Yekaterinburg',
            'Asia/Yerevan' => 'Asia/Yerevan',
            'Atlantic/Azores' => 'Atlantic/Azores',
            'Atlantic/Bermuda' => 'Atlantic/Bermuda',
            'Atlantic/Canary' => 'Atlantic/Canary',
            'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde',
            'Atlantic/Faroe' => 'Atlantic/Faroe',
            'Atlantic/Madeira' => 'Atlantic/Madeira',
            'Atlantic/Reykjavik' => 'Atlantic/Reykjavik',
            'Atlantic/South_Georgia' => 'Atlantic/South_Georgia',
            'Atlantic/St_Helena' => 'Atlantic/St_Helena',
            'Atlantic/Stanley' => 'Atlantic/Stanley',
            'Australia/Adelaide' => 'Australia/Adelaide',
            'Australia/Brisbane' => 'Australia/Brisbane',
            'Australia/Broken_Hill' => 'Australia/Broken_Hill',
            'Australia/Currie' => 'Australia/Currie',
            'Australia/Darwin' => 'Australia/Darwin',
            'Australia/Hobart' => 'Australia/Hobart',
            'Australia/Lindeman' => 'Australia/Lindeman',
            'Australia/Lord_Howe' => 'Australia/Lord_Howe',
            'Australia/Melbourne' => 'Australia/Melbourne',
            'Australia/Perth' => 'Australia/Perth',
            'Australia/Sydney' => 'Australia/Sydney',
            'Europe/Amsterdam' => 'Europe/Amsterdam',
            'Europe/Andorra' => 'Europe/Andorra',
            'Europe/Astrakhan' => 'Europe/Astrakhan',
            'Europe/Athens' => 'Europe/Athens',
            'Europe/Belgrade' => 'Europe/Belgrade',
            'Europe/Berlin' => 'Europe/Berlin',
            'Europe/Bratislava' => 'Europe/Bratislava',
            'Europe/Brussels' => 'Europe/Brussels',
            'Europe/Bucharest' => 'Europe/Bucharest',
            'Europe/Budapest' => 'Europe/Budapest',
            'Europe/Busingen' => 'Europe/Busingen',
            'Europe/Chisinau' => 'Europe/Chisinau',
            'Europe/Copenhagen' => 'Europe/Copenhagen',
            'Europe/Dublin' => 'Europe/Dublin',
            'Europe/Gibraltar' => 'Europe/Gibraltar',
            'Europe/Guernsey' => 'Europe/Guernsey',
            'Europe/Helsinki' => 'Europe/Helsinki',
            'Europe/Isle_of_Man' => 'Europe/Isle_of_Man',
            'Europe/Istanbul' => 'Europe/Istanbul',
            'Europe/Jersey' => 'Europe/Jersey',
            'Europe/Kaliningrad' => 'Europe/Kaliningrad',
            'Europe/Kiev' => 'Europe/Kiev',
            'Europe/Kosice' => 'Europe/Kosice',
            'Europe/Lisbon' => 'Europe/Lisbon',
            'Europe/Ljubljana' => 'Europe/Ljubljana',
            'Europe/London' => 'Europe/London',
            'Europe/Luxembourg' => 'Europe/Luxembourg',
            'Europe/Madrid' => 'Europe/Madrid',
            'Europe/Malta' => 'Europe/Malta',
            'Europe/Mariehamn' => 'Europe/Mariehamn',
            'Europe/Minsk' => 'Europe/Minsk',
            'Europe/Monaco' => 'Europe/Monaco',
            'Europe/Moscow' => 'Europe/Moscow',
            'Europe/Nicosia' => 'Europe/Nicosia',
            'Europe/Oslo' => 'Europe/Oslo',
            'Europe/Paris' => 'Europe/Paris',
            'Europe/Podgorica' => 'Europe/Podgorica',
            'Europe/Prague' => 'Europe/Prague',
            'Europe/Riga' => 'Europe/Riga',
            'Europe/Rome' => 'Europe/Rome',
            'Europe/Samara' => 'Europe/Samara',
            'Europe/San_Marino' => 'Europe/San_Marino',
            'Europe/Sarajevo' => 'Europe/Sarajevo',
            'Europe/Saratov' => 'Europe/Saratov',
            'Europe/Simferopol' => 'Europe/Simferopol',
            'Europe/Skopje' => 'Europe/Skopje',
            'Europe/Sofia' => 'Europe/Sofia',
            'Europe/Stockholm' => 'Europe/Stockholm',
            'Europe/Tallinn' => 'Europe/Tallinn',
            'Europe/Tirane' => 'Europe/Tirane',
            'Europe/Uzhgorod' => 'Europe/Uzhgorod',
            'Europe/Vaduz' => 'Europe/Vaduz',
            'Europe/Vatican' => 'Europe/Vatican',
            'Europe/Vienna' => 'Europe/Vienna',
            'Europe/Vilnius' => 'Europe/Vilnius',
            'Europe/Volgograd' => 'Europe/Volgograd',
            'Europe/Warsaw' => 'Europe/Warsaw',
            'Europe/Zagreb' => 'Europe/Zagreb',
            'Europe/Zaporozhye' => 'Europe/Zaporozhye',
            'Europe/Zurich' => 'Europe/Zurich',
            'Indian/Antananarivo' => 'Indian/Antananarivo',
            'Indian/Chagos' => 'Indian/Chagos',
            'Indian/Christmas' => 'Indian/Christmas',
            'Indian/Cocos' => 'Indian/Cocos',
            'Indian/Comoro' => 'Indian/Comoro',
            'Indian/Kerguelen' => 'Indian/Kerguelen',
            'Indian/Mahe' => 'Indian/Mahe',
            'Indian/Maldives' => 'Indian/Maldives',
            'Indian/Mauritius' => 'Indian/Mauritius',
            'Indian/Reunion' => 'Indian/Reunion',
            'Pacific/Apia' => 'Pacific/Apia',
            'Pacific/Auckland' => 'Pacific/Auckland',
            'Pacific/Bougainville' => 'Pacific/Bougainville',
            'Pacific/Chatham' => 'Pacific/Chatham',
            'Pacific/Chuuk' => 'Pacific/Chuuk',
            'Pacific/Efate' => 'Pacific/Efate',
            'Pacific/Enderbury' => 'Pacific/Enderbury',
            'Pacific/Fakaofo' => 'Pacific/Fakaofo',
            'Pacific/Fiji' => 'Pacific/Fiji',
            'Pacific/Funafuti' => 'Pacific/Funafuti',
            'Pacific/Galapagos' => 'Pacific/Galapagos',
            'Pacific/Gambier' => 'Pacific/Gambier',
            'Pacific/Guadalcanal' => 'Pacific/Guadalcanal',
            'Pacific/Guam' => 'Pacific/Guam',
            'Pacific/Kiritimati' => 'Pacific/Kiritimati',
            'Pacific/Kosrae' => 'Pacific/Kosrae',
            'Pacific/Kwajalein' => 'Pacific/Kwajalein',
            'Pacific/Majuro' => 'Pacific/Majuro',
            'Pacific/Marquesas' => 'Pacific/Marquesas',
            'Pacific/Nauru' => 'Pacific/Nauru',
            'Pacific/Niue' => 'Pacific/Niue',
            'Pacific/Norfolk' => 'Pacific/Norfolk',
            'Pacific/Noumea' => 'Pacific/Noumea',
            'Pacific/Pago_Pago' => 'Pacific/Pago_Pago',
            'Pacific/Palau' => 'Pacific/Palau',
            'Pacific/Pitcairn' => 'Pacific/Pitcairn',
            'Pacific/Ponape' => 'Pacific/Ponape',
            'Pacific/Port_Moresby' => 'Pacific/Port_Moresby',
            'Pacific/Rarotonga' => 'Pacific/Rarotonga',
            'Pacific/Saipan' => 'Pacific/Saipan',
            'Pacific/Tahiti' => 'Pacific/Tahiti',
            'Pacific/Tarawa' => 'Pacific/Tarawa',
            'Pacific/Tongatapu' => 'Pacific/Tongatapu',
            'Pacific/Wake' => 'Pacific/Wake',
            'Pacific/Wallis' => 'Pacific/Wallis',
        ];

        return $timezones;
    }
}

if (!function_exists('country_codes')) {
    function country_codes()
    {
        $countryCodes = [
            '1' => 'United States (+1)',
            '7' => 'Russia (+7)',
            '20' => 'Egypt (+20)',
            '27' => 'South Africa (+27)',
            '30' => 'Greece (+30)',
            '31' => 'Netherlands (+31)',
            '32' => 'Belgium (+32)',
            '33' => 'France (+33)',
            '34' => 'Spain (+34)',
            '36' => 'Hungary (+36)',
            '39' => 'Italy (+39)',
            '40' => 'Romania (+40)',
            '41' => 'Switzerland (+41)',
            '43' => 'Austria (+43)',
            '44' => 'United Kingdom (+44)',
            '45' => 'Denmark (+45)',
            '46' => 'Sweden (+46)',
            '47' => 'Norway (+47)',
            '48' => 'Poland (+48)',
            '49' => 'Germany (+49)',
            '51' => 'Peru (+51)',
            '52' => 'Mexico (+52)',
            '53' => 'Cuba (+53)',
            '54' => 'Argentina (+54)',
            '55' => 'Brazil (+55)',
            '56' => 'Chile (+56)',
            '57' => 'Colombia (+57)',
            '58' => 'Venezuela (+58)',
            '60' => 'Malaysia (+60)',
            '61' => 'Australia (+61)',
            '62' => 'Indonesia (+62)',
            '63' => 'Philippines (+63)',
            '64' => 'New Zealand (+64)',
            '65' => 'Singapore (+65)',
            '66' => 'Thailand (+66)',
            '81' => 'Japan (+81)',
            '82' => 'South Korea (+82)',
            '84' => 'Vietnam (+84)',
            '86' => 'China (+86)',
            '90' => 'Turkey (+90)',
            '91' => 'India (+91)',
            '92' => 'Pakistan (+92)',
            '93' => 'Afghanistan (+93)',
            '94' => 'Sri Lanka (+94)',
            '95' => 'Myanmar (+95)',
            '98' => 'Iran (+98)',
            '212' => 'Morocco (+212)',
            '213' => 'Algeria (+213)',
            '216' => 'Tunisia (+216)',
            '218' => 'Libya (+218)',
            '220' => 'The Gambia (+220)',
            '221' => 'Senegal (+221)',
            '222' => 'Mauritania (+222)',
            '223' => 'Mali (+223)',
            '224' => 'Guinea (+224)',
            '225' => 'Côte d\'Ivoire (+225)',
            '226' => 'Burkina Faso (+226)',
            '227' => 'Niger (+227)',
            '228' => 'Togo (+228)',
            '229' => 'Benin (+229)',
            '230' => 'Mauritius (+230)',
            '231' => 'Liberia (+231)',
            '232' => 'Sierra Leone (+232)',
            '233' => 'Ghana (+233)',
            '234' => 'Nigeria (+234)',
            '235' => 'Chad (+235)',
            '236' => 'Central African Republic (+236)',
            '237' => 'Cameroon (+237)',
            '238' => 'Cape Verde (+238)',
            '239' => 'São Tomé and Príncipe (+239)',
            '240' => 'Equatorial Guinea (+240)',
            '241' => 'Gabon (+241)',
            '242' => 'Congo (+242)',
            '243' => 'Democratic Republic of the Congo (+243)',
            '244' => 'Angola (+244)',
            '245' => 'Guinea-Bissau (+245)',
            '246' => 'Ascension Island (+246)',
            '247' => 'Saint Helena (+247)',
            '248' => 'Seychelles (+248)',
            '249' => 'Sudan (+249)',
            '250' => 'Rwanda (+250)',
            '251' => 'Ethiopia (+251)',
            '252' => 'Somalia (+252)',
            '253' => 'Djibouti (+253)',
            '254' => 'Kenya (+254)',
            '255' => 'Tanzania (+255)',
            '256' => 'Uganda (+256)',
            '257' => 'Burundi (+257)',
            '258' => 'Mozambique (+258)',
            '260' => 'Zambia (+260)',
            '261' => 'Madagascar (+261)',
            '262' => 'Réunion (+262)',
            '263' => 'Zimbabwe (+263)',
            '264' => 'Namibia (+264)',
            '265' => 'Malawi (+265)',
            '266' => 'Lesotho (+266)',
            '267' => 'Botswana (+267)',
            '268' => 'Eswatini (+268)',
            '269' => 'Comoros (+269)',
            '290' => 'Saint Helena (+290)',
            '291' => 'Eritrea (+291)',
            '297' => 'Aruba (+297)',
            '298' => 'Faroe Islands (+298)',
            '299' => 'Greenland (+299)',
            '350' => 'Gibraltar (+350)',
            '351' => 'Portugal (+351)',
            '352' => 'Luxembourg (+352)',
            '353' => 'Ireland (+353)',
            '354' => 'Iceland (+354)',
            '355' => 'Albania (+355)',
            '356' => 'Malta (+356)',
            '357' => 'Cyprus (+357)',
            '358' => 'Finland (+358)',
            '359' => 'Bulgaria (+359)',
            '370' => 'Lithuania (+370)',
            '371' => 'Latvia (+371)',
            '372' => 'Estonia (+372)',
            '373' => 'Moldova (+373)',
            '374' => 'Armenia (+374)',
            '375' => 'Belarus (+375)',
            '376' => 'Andorra (+376)',
            '377' => 'Monaco (+377)',
            '378' => 'San Marino (+378)',
            '379' => 'Vatican City (+379)',
            '380' => 'Ukraine (+380)',
            '381' => 'Serbia (+381)',
            '382' => 'Montenegro (+382)',
            '383' => 'Kosovo (+383)',
            '385' => 'Croatia (+385)',
            '386' => 'Slovenia (+386)',
            '387' => 'Bosnia and Herzegovina (+387)',
            '388' => 'Yugoslavia (+388)',
            '389' => 'North Macedonia (+389)',
            '420' => 'Czech Republic (+420)',
            '421' => 'Slovakia (+421)',
            '423' => 'Liechtenstein (+423)',
            '500' => 'Falkland Islands (+500)',
            '501' => 'Belize (+501)',
            '502' => 'Guatemala (+502)',
            '503' => 'El Salvador (+503)',
            '504' => 'Honduras (+504)',
            '505' => 'Nicaragua (+505)',
            '506' => 'Costa Rica (+506)',
            '507' => 'Panama (+507)',
            '508' => 'Saint Pierre and Miquelon (+508)',
            '509' => 'Haiti (+509)',
            '590' => 'Guadeloupe (+590)',
            '591' => 'Bolivia (+591)',
            '592' => 'Guyana (+592)',
            '593' => 'Ecuador (+593)',
            '594' => 'French Guiana (+594)',
            '595' => 'Paraguay (+595)',
            '596' => 'Martinique (+596)',
            '597' => 'Suriname (+597)',
            '598' => 'Uruguay (+598)',
            '599' => 'Curaçao (+599)',
            '670' => 'East Timor (+670)',
            '672' => 'Australian External Territories (+672)',
            '673' => 'Brunei (+673)',
            '674' => 'Nauru (+674)',
            '675' => 'Papua New Guinea (+675)',
            '676' => 'Tonga (+676)',
            '677' => 'Solomon Islands (+677)',
            '678' => 'Vanuatu (+678)',
            '679' => 'Fiji (+679)',
            '680' => 'Palau (+680)',
            '681' => 'Wallis and Futuna (+681)',
            '682' => 'Cook Islands (+682)',
            '683' => 'Niue (+683)',
            '684' => 'American Samoa (+684)',
            '685' => 'Samoa (+685)',
            '686' => 'Tuvalu (+686)',
            '687' => 'New Caledonia (+687)',
            '688' => 'Tuvalu (+688)',
            '689' => 'French Polynesia (+689)',
            '690' => 'Tokelau (+690)',
            '691' => 'Micronesia (+691)',
            '692' => 'Marshall Islands (+692)',
            '693' => 'Palau (+693)',
            '850' => 'North Korea (+850)',
            '852' => 'Hong Kong (+852)',
            '853' => 'Macau (+853)',
            '855' => 'Cambodia (+855)',
            '856' => 'Laos (+856)',
            '880' => 'Bangladesh (+880)',
            '881' => 'Global Mobile Satellite System (+881)',
            '882' => 'Global Mobile Satellite System (+882)',
            '883' => 'Global Mobile Satellite System (+883)',
            '886' => 'Taiwan (+886)',
            '888' => 'Global Mobile Satellite System (+888)',
        ];

        return $countryCodes;
    }
}

if (!function_exists('currencies')) {
    function currencies()
    {
        $currencies = [
            // Major Currencies
            '$' => 'USD - United States Dollar',
            '€' => 'EUR - Euro',
            '¥' => 'JPY - Japanese Yen',
            '£' => 'GBP - British Pound Sterling',
            'CHF' => 'CHF - Swiss Franc',
            'C$' => 'CAD - Canadian Dollar',
            'AU$' => 'AUD - Australian Dollar',
            'NZ$' => 'NZD - New Zealand Dollar',

            // Asian Currencies
            'Rp' => 'IDR - Indonesian Rupiah',
            '₹' => 'INR - Indian Rupee',
            '₩' => 'KRW - South Korean Won',
            'CN¥' => 'CNY - Chinese Yuan',
            'HK$' => 'HKD - Hong Kong Dollar',
            'SGD' => 'SGD - Singapore Dollar',
            'MYR' => 'MYR - Malaysian Ringgit',
            '฿' => 'THB - Thai Baht',
            '₱' => 'PHP - Philippine Peso',
            '₫' => 'VND - Vietnamese Dong',
            'NT$' => 'TWD - New Taiwan Dollar',
            'Rs' => 'PKR - Pakistani Rupee',
            'Tk' => 'BDT - Bangladeshi Taka',
            'Rs' => 'LKR - Sri Lankan Rupee',
            'K' => 'MMK - Myanmar Kyat',
            '₭' => 'LAK - Lao Kip',
            '៛' => 'KHR - Cambodian Riel',
            'BN$' => 'BND - Brunei Dollar',
            'MOP' => 'MOP - Macanese Pataca',

            // Middle East Currencies
            'SAR' => 'SAR - Saudi Riyal',
            'AED' => 'AED - UAE Dirham',
            'QAR' => 'QAR - Qatari Riyal',
            'KD' => 'KWD - Kuwaiti Dinar',
            'BD' => 'BHD - Bahraini Dinar',
            'OMR' => 'OMR - Omani Rial',
            '₪' => 'ILS - Israeli New Shekel',
            'JD' => 'JOD - Jordanian Dinar',
            '£' => 'EGP - Egyptian Pound',
            '₺' => 'TRY - Turkish Lira',
            'ریال' => 'IRR - Iranian Rial',

            // European Currencies
            'kr' => 'NOK - Norwegian Krone',
            'kr' => 'SEK - Swedish Krona',
            'kr' => 'DKK - Danish Krone',
            'kr' => 'ISK - Icelandic Króna',
            'zł' => 'PLN - Polish Zloty',
            'Kč' => 'CZK - Czech Koruna',
            'Ft' => 'HUF - Hungarian Forint',
            'lei' => 'RON - Romanian Leu',
            'лв' => 'BGN - Bulgarian Lev',
            'kn' => 'HRK - Croatian Kuna',
            'RSD' => 'RSD - Serbian Dinar',
            '₴' => 'UAH - Ukrainian Hryvnia',
            'Br' => 'BYN - Belarusian Ruble',
            '₽' => 'RUB - Russian Ruble',

            // Americas Currencies
            'R$' => 'BRL - Brazilian Real',
            'Mex$' => 'MXN - Mexican Peso',
            'AR$' => 'ARS - Argentine Peso',
            'CLP' => 'CLP - Chilean Peso',
            'COL$' => 'COP - Colombian Peso',
            'S/' => 'PEN - Peruvian Sol',
            'Bs.' => 'VES - Venezuelan Bolívar',
            'Bs' => 'BOB - Bolivian Boliviano',
            '₡' => 'CRC - Costa Rican Colón',
            '$' => 'CUP - Cuban Peso',
            'RD$' => 'DOP - Dominican Peso',
            'GTQ' => 'GTQ - Guatemalan Quetzal',
            'L' => 'HNL - Honduran Lempira',
            'C$' => 'NIO - Nicaraguan Córdoba',
            'B/.' => 'PAB - Panamanian Balboa',
            '₲' => 'PYG - Paraguayan Guaraní',
            '$U' => 'UYU - Uruguayan Peso',
            'GY$' => 'GYD - Guyanese Dollar',
            'SR$' => 'SRD - Surinamese Dollar',

            // African Currencies
            'ZAR' => 'ZAR - South African Rand',
            '₦' => 'NGN - Nigerian Naira',
            'KSh' => 'KES - Kenyan Shilling',
            'GH₵' => 'GHS - Ghanaian Cedi',
            'TSh' => 'TZS - Tanzanian Shilling',
            'USh' => 'UGX - Ugandan Shilling',
            'ብር' => 'ETB - Ethiopian Birr',
            'E' => 'SZL - Swazi Lilangeni',
            'D' => 'GMD - Gambian Dalasi',
            'FG' => 'GNF - Guinean Franc',
            'CFA' => 'XAF - Central African CFA Franc',
            'CFA' => 'XOF - West African CFA Franc',
            'Fdj' => 'DJF - Djiboutian Franc',
            'Nfk' => 'ERN - Eritrean Nakfa',
            'FRw' => 'RWF - Rwandan Franc',
            'Sh.So.' => 'SOS - Somali Shilling',
            'SDG' => 'SDG - Sudanese Pound',
            'DA' => 'DZD - Algerian Dinar',
            'Dh' => 'MAD - Moroccan Dirham',
            'DT' => 'TND - Tunisian Dinar',
            'LD' => 'LYD - Libyan Dinar',
            'MK' => 'MWK - Malawian Kwacha',
            'MRU' => 'MRU - Mauritanian Ouguiya',
            '₨' => 'MUR - Mauritian Rupee',
            'MT' => 'MZN - Mozambican Metical',
            'N$' => 'NAD - Namibian Dollar',
            'P' => 'BWP - Botswana Pula',
            'Le' => 'SLL - Sierra Leonean Leone',
            'ZK' => 'ZMW - Zambian Kwacha',
            'Z$' => 'ZWL - Zimbabwean Dollar',

            // Oceania Currencies
            'FJ$' => 'FJD - Fijian Dollar',
            'T' => 'TOP - Tongan Paʻanga',
            'WS$' => 'WST - Samoan Tālā',
            'VT' => 'VUV - Vanuatu Vatu',
            'SI$' => 'SBD - Solomon Islands Dollar',
            'F' => 'XPF - CFP Franc',
            'K' => 'PGK - Papua New Guinean Kina',

            // Cryptocurrencies (Optional)
            '₿' => 'BTC - Bitcoin',
            'Ξ' => 'ETH - Ethereum',
        ];

        return $currencies;
    }
}

if (!function_exists('user_setting')) {

    function user_setting()
    {
        return Setting::first(['timezone']);
    }
}

if (!function_exists('get_mime_format')) {
    /**
     * Get the format from MIME type
     *
     * @param string $mime
     * @return string|null
     */
    function get_mime_format($mime)
    {

        if ($mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return 'docx';
        }

        if ($mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return 'xlsx';
        }

        if (!is_string($mime)) {
            throw new InvalidArgumentException('Invalid MIME type');
        }

        // Split the MIME string using the '/' separator
        $parts = explode('/', $mime);

        // If the split does not result in two parts, it's not a valid MIME
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Invalid MIME type format');
        }

        // Return the second part which represents the format
        return $parts[1];
    }
}

if (!function_exists('detect_link_message')) {
    /**
     * Get the format from MIME type
     *
     * @param string $mime
     * @return string|null
     */
    function detect_link_message($text)
    {

        $pattern = '/(http|https|ftp|ftps):\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/\S*)?/';
        return preg_replace($pattern, '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>', $text);
    }
}

if (!function_exists('platform_currency')) {
    /**
     * Get the format from MIME type
     *
     * @param string $mime
     * @return string|null
     */
    function platform_currency()
    {
        $setting = InternalSetting::first(['currency', 'currency_position', 'fb_app_id', 'fb_app_secret', 'fb_config_id', 'instagram_config_id', 'messanger_config_id', 'ig_app_id', 'ig_app_secret']);
        return $setting ?? '';
    }
}

if (!function_exists('my_business')) {
    /**
     * Get the format from MIME type
     *
     * @param string $mime
     * @return string|null
     */
    function my_business()
    {
        return session()->get('businessid');;
    }
}

if (!function_exists('get_package_active')) {
    /**
     * Get the format from MIME type
     *
     * @param string $mime
     * @return string|null
     */
    function get_package_active($businessID)
    {
        $business = Setting::where('id', $businessID)->first(['id']);
        return session()->get('businessid');;
    }
}

if (!function_exists('icon_link')) {
    /**
     * Set the active class to the current opened menu.
     *
     * @param  string|array $route
     * @param  string       $className
     * @return string
     */
    function icon_link($tipe)
    {
        $url = null;

        if ($tipe == 'instagram') {
            $url    = asset('assets/img/default/instagram.png');
        }

        if ($tipe == 'facebook') {
            $url    = asset('assets/img/default/facebook.png');
        }

        if ($tipe == 'twitter') {
            $url    = asset('assets/img/default/twitter.png');
        }

        if ($tipe == 'youtube') {
            $url    = asset('assets/img/default/youtube.png');
        }

        if ($tipe == 'website') {
            $url    = asset('assets/img/default/world-wide-web.png');
        }

        if ($tipe == 'whatsapp') {
            $url    = asset('assets/img/default/whatsapp.png');
        }

        return $url;
    }
}

if (!function_exists('chatbot_limitation')) {

    function chatbot_limitation($businessId)
    {
        $business   = Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;

        if ($package) {
            if ($package->limit_chatbot == 'yes') {
                $chatbots   = ChatBot::where('business_id', $businessId)->count();
                if ($chatbots >= $package->chatbot_limit) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}

if (!function_exists('device_limitation')) {

    function device_limitation($businessId)
    {
        $business   = Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_device == 'yes') {
                $devices   = WhatsappDevice::where('business_id', $businessId)->count();
                if ($devices >= $package->device_limit) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}

if (!function_exists('template_limitation')) {

    function template_limitation($businessId)
    {
        $business   = Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_template == 'yes') {
                $templates   = MessageTemplate::where('business_id', $businessId)->count();
                if ($templates >= $package->template_limit) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}

if (!function_exists('ai_agent_limitation')) {

    function ai_agent_limitation($businessId)
    {
        $business   = Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_ai_training == 'yes') {
                $trainings   = FineTunnel::where('business_id', $businessId)->count();
                if ($trainings >= $package->ai_training_limit) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}

if (!function_exists('live_chat_limitation')) {

    function live_chat_limitation($businessId)
    {
        $business   = Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->livechat_limit == 'yes') {
                $livechats   = LiveChat::where('business_id', $businessId)->count();
                if ($livechats >= $package->limit_livechat) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}

if (!function_exists('check_courier')) {
    /**
     * Get the format from MIME type
     *
     * @param string $mime
     * @return string|null
     */
    function check_courier($courierCode, $fineTunnelId)
    {
        return CourierFineTunnel::where('code', $courierCode)->where('finetunnel_id', $fineTunnelId)->first(['id']);
    }
}

if (!function_exists('device_array')) {

    function device_array($data)
    {
        $listDevices = WhatsappKeyAccount::where(function ($query) use ($data) {
            return $query->whereIn('id', $data);
        })->orderBy("phone", "asc")->get(['id', 'phone'])->pluck('id');

        return $listDevices;
    }
}

if (!function_exists('current_lang')) {

    function current_lang()
    {
        return app()->getLocale();
    }
}

if (!function_exists('my_user')) {

    function my_user()
    {
        return auth()->user();
    }
}

if (!function_exists('check_user')) {

    function check_user()
    {
        return auth()->check();
    }
}

if (!function_exists('currency_format')) {

    function currency_format($number)
    {
        $setting    = InternalSetting::first(['currency', 'currency_position']);
        $number     = ($setting->currency_position == 'start' ? $setting->currency . ' ' : '') . number_format($number) . ($setting->currency_position == 'end' ? ' ' . $setting->currency : '');
        return $number;
    }
}

if (!function_exists('instagram_limitation')) {

    function instagram_limitation($businessId)
    {
        $business   = \App\Models\Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_instagram == 'yes') {
                $accounts = \App\Models\Meta\InstagramAccount::where('business_id', $businessId)->count();
                if ($accounts >= $package->instagram) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}

if (!function_exists('telegram_limitation')) {

    function telegram_limitation($businessId)
    {
        $business   = \App\Models\Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_telegram == 'yes') {
                $accounts = \App\Models\TelegramKey::where('business_id', $businessId)->count();
                if ($accounts >= $package->telegram) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}

if (!function_exists('messenger_limitation')) {

    function messenger_limitation($businessId)
    {
        $business   = \App\Models\Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_messanger == 'yes') {
                $accounts = \App\Models\Meta\MessengerAccount::where('business_id', $businessId)->count();
                if ($accounts >= $package->messanger) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}

if (!function_exists('waba_limitation')) {

    function waba_limitation($businessId)
    {
        $business   = \App\Models\Setting::where('id', $businessId)->first(['id']);
        $package    = $business->package_active;
        if ($package) {
            if ($package->limit_waba == 'yes') {
                $accounts = \App\Models\MetaAccount::where('business_id', $businessId)->where('type', 'waba')->count();
                if ($accounts >= $package->waba_limit) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}

if (!function_exists('cache_asset')) {
    /**
     * Cache-busted asset URL
     * Usage: cache_asset('assets/libs/jquery/jquery.js')
     */
    function cache_asset($path)
    {
        $version = config('app.version', '1.0.0');
        return asset($path) . '?v=' . $version;
    }
}
