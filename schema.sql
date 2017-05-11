CREATE TABLE `tbl_users_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `user_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `user_firstname` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `user_lastname` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `user_gender` enum('M','F') DEFAULT NULL,
  `user_birthday` date DEFAULT NULL,
  `user_timezone` enum('Africa/Abidjan','Africa/Accra','Africa/Addis_Ababa','Africa/Algiers','Africa/Asmara','Africa/Bamako','Africa/Bangui','Africa/Banjul','Africa/Bissau','Africa/Blantyre','Africa/Brazzaville','Africa/Bujumbura','Africa/Cairo','Africa/Casablanca','Africa/Ceuta','Africa/Conakry','Africa/Dakar','Africa/Dar_es_Salaam','Africa/Djibouti','Africa/Douala','Africa/El_Aaiun','Africa/Freetown','Africa/Gaborone','Africa/Harare','Africa/Johannesburg','Africa/Juba','Africa/Kampala','Africa/Khartoum','Africa/Kigali','Africa/Kinshasa','Africa/Lagos','Africa/Libreville','Africa/Lome','Africa/Luanda','Africa/Lubumbashi','Africa/Lusaka','Africa/Malabo','Africa/Maputo','Africa/Maseru','Africa/Mbabane','Africa/Mogadishu','Africa/Monrovia','Africa/Nairobi','Africa/Ndjamena','Africa/Niamey','Africa/Nouakchott','Africa/Ouagadougou','Africa/Porto-Novo','Africa/Sao_Tome','Africa/Tripoli','Africa/Tunis','Africa/Windhoek','America/Adak','America/Anchorage','America/Anguilla','America/Antigua','America/Araguaina','America/Argentina/Buenos_Aires','America/Argentina/Catamarca','America/Argentina/Cordoba','America/Argentina/Jujuy','America/Argentina/La_Rioja','America/Argentina/Mendoza','America/Argentina/Rio_Gallegos','America/Argentina/Salta','America/Argentina/San_Juan','America/Argentina/San_Luis','America/Argentina/Tucuman','America/Argentina/Ushuaia','America/Aruba','America/Asuncion','America/Atikokan','America/Bahia','America/Bahia_Banderas','America/Barbados','America/Belem','America/Belize','America/Blanc-Sablon','America/Boa_Vista','America/Bogota','America/Boise','America/Cambridge_Bay','America/Campo_Grande','America/Cancun','America/Caracas','America/Cayenne','America/Cayman','America/Chicago','America/Chihuahua','America/Costa_Rica','America/Creston','America/Cuiaba','America/Curacao','America/Danmarkshavn','America/Dawson','America/Dawson_Creek','America/Denver','America/Detroit','America/Dominica','America/Edmonton','America/Eirunepe','America/El_Salvador','America/Fort_Nelson','America/Fortaleza','America/Glace_Bay','America/Godthab','America/Goose_Bay','America/Grand_Turk','America/Grenada','America/Guadeloupe','America/Guatemala','America/Guayaquil','America/Guyana','America/Halifax','America/Havana','America/Hermosillo','America/Indiana/Indianapolis','America/Indiana/Knox','America/Indiana/Marengo','America/Indiana/Petersburg','America/Indiana/Tell_City','America/Indiana/Vevay','America/Indiana/Vincennes','America/Indiana/Winamac','America/Inuvik','America/Iqaluit','America/Jamaica','America/Juneau','America/Kentucky/Louisville','America/Kentucky/Monticello','America/Kralendijk','America/La_Paz','America/Lima','America/Los_Angeles','America/Lower_Princes','America/Maceio','America/Managua','America/Manaus','America/Marigot','America/Martinique','America/Matamoros','America/Mazatlan','America/Menominee','America/Merida','America/Metlakatla','America/Mexico_City','America/Miquelon','America/Moncton','America/Monterrey','America/Montevideo','America/Montserrat','America/Nassau','America/New_York','America/Nipigon','America/Nome','America/Noronha','America/North_Dakota/Beulah','America/North_Dakota/Center','America/North_Dakota/New_Salem','America/Ojinaga','America/Panama','America/Pangnirtung','America/Paramaribo','America/Phoenix','America/Port-au-Prince','America/Port_of_Spain','America/Porto_Velho','America/Puerto_Rico','America/Rainy_River','America/Rankin_Inlet','America/Recife','America/Regina','America/Resolute','America/Rio_Branco','America/Santarem','America/Santa_Isabel','America/Santiago','America/Santo_Domingo','America/Sao_Paulo','America/Scoresbysund','America/Sitka','America/St_Barthelemy','America/St_Johns','America/St_Kitts','America/St_Lucia','America/St_Thomas','America/St_Vincent','America/Swift_Current','America/Tegucigalpa','America/Thule','America/Thunder_Bay','America/Tijuana','America/Toronto','America/Tortola','America/Vancouver','America/Whitehorse','America/Winnipeg','America/Yakutat','America/Yellowknife','Antarctica/Casey','Antarctica/Davis','Antarctica/DumontDUrville','Antarctica/Macquarie','Antarctica/Mawson','Antarctica/McMurdo','Antarctica/Palmer','Antarctica/Rothera','Antarctica/Syowa','Antarctica/Troll','Antarctica/Vostok','Arctic/Longyearbyen','Asia/Aden','Asia/Almaty','Asia/Amman','Asia/Anadyr','Asia/Aqtau','Asia/Aqtobe','Asia/Ashgabat','Asia/Baghdad','Asia/Bahrain','Asia/Baku','Asia/Bangkok','Asia/Barnaul','Asia/Beirut','Asia/Bishkek','Asia/Brunei','Asia/Chita','Asia/Choibalsan','Asia/Colombo','Asia/Damascus','Asia/Dhaka','Asia/Dili','Asia/Dubai','Asia/Dushanbe','Asia/Gaza','Asia/Hebron','Asia/Ho_Chi_Minh','Asia/Hong_Kong','Asia/Hovd','Asia/Irkutsk','Asia/Jakarta','Asia/Jayapura','Asia/Jerusalem','Asia/Kabul','Asia/Kamchatka','Asia/Karachi','Asia/Kathmandu','Asia/Khandyga','Asia/Kolkata','Asia/Krasnoyarsk','Asia/Kuala_Lumpur','Asia/Kuching','Asia/Kuwait','Asia/Macau','Asia/Magadan','Asia/Makassar','Asia/Manila','Asia/Muscat','Asia/Nicosia','Asia/Novokuznetsk','Asia/Novosibirsk','Asia/Omsk','Asia/Oral','Asia/Phnom_Penh','Asia/Pontianak','Asia/Pyongyang','Asia/Qatar','Asia/Qyzylorda','Asia/Rangoon','Asia/Riyadh','Asia/Sakhalin','Asia/Samarkand','Asia/Seoul','Asia/Shanghai','Asia/Singapore','Asia/Srednekolymsk','Asia/Taipei','Asia/Tashkent','Asia/Tbilisi','Asia/Tehran','Asia/Thimphu','Asia/Tokyo','Asia/Ulaanbaatar','Asia/Urumqi','Asia/Ust-Nera','Asia/Vientiane','Asia/Vladivostok','Asia/Yakutsk','Asia/Yekaterinburg','Asia/Yerevan','Atlantic/Azores','Atlantic/Bermuda','Atlantic/Canary','Atlantic/Cape_Verde','Atlantic/Faroe','Atlantic/Madeira','Atlantic/Reykjavik','Atlantic/South_Georgia','Atlantic/St_Helena','Atlantic/Stanley','Australia/Adelaide','Australia/Brisbane','Australia/Broken_Hill','Australia/Currie','Australia/Darwin','Australia/Eucla','Australia/Hobart','Australia/Lindeman','Australia/Lord_Howe','Australia/Melbourne','Australia/Perth','Australia/Sydney','Europe/Amsterdam','Europe/Andorra','Europe/Astrakhan','Europe/Athens','Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels','Europe/Bucharest','Europe/Budapest','Europe/Busingen','Europe/Chisinau','Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey','Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul','Europe/Jersey','Europe/Kaliningrad','Europe/Kiev','Europe/Lisbon','Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid','Europe/Malta','Europe/Mariehamn','Europe/Minsk','Europe/Monaco','Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Podgorica','Europe/Prague','Europe/Riga','Europe/Rome','Europe/Samara','Europe/San_Marino','Europe/Sarajevo','Europe/Simferopol','Europe/Skopje','Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Tirane','Europe/Ulyanovsk','Europe/Uzhgorod','Europe/Vaduz','Europe/Vatican','Europe/Vienna','Europe/Vilnius','Europe/Volgograd','Europe/Warsaw','Europe/Zagreb','Europe/Zaporozhye','Europe/Zurich','Indian/Antananarivo','Indian/Chagos','Indian/Christmas','Indian/Cocos','Indian/Comoro','Indian/Kerguelen','Indian/Mahe','Indian/Maldives','Indian/Mauritius','Indian/Mayotte','Indian/Reunion','Pacific/Apia','Pacific/Auckland','Pacific/Bougainville','Pacific/Chatham','Pacific/Chuuk','Pacific/Easter','Pacific/Efate','Pacific/Enderbury','Pacific/Fakaofo','Pacific/Fiji','Pacific/Funafuti','Pacific/Galapagos','Pacific/Gambier','Pacific/Guadalcanal','Pacific/Guam','Pacific/Honolulu','Pacific/Johnston','Pacific/Kiritimati','Pacific/Kosrae','Pacific/Kwajalein','Pacific/Majuro','Pacific/Marquesas','Pacific/Midway','Pacific/Nauru','Pacific/Niue','Pacific/Norfolk','Pacific/Noumea','Pacific/Pago_Pago','Pacific/Palau','Pacific/Pitcairn','Pacific/Pohnpei','Pacific/Port_Moresby','Pacific/Rarotonga','Pacific/Saipan','Pacific/Tahiti','Pacific/Tarawa','Pacific/Tongatapu','Pacific/Wake','Pacific/Wallis','UTC') DEFAULT NULL,
  `user_login_date` datetime DEFAULT NULL,
  `user_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `ix_user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;

CREATE TABLE `tbl_users_auth` (
  `auth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auth_user_id` int(10) unsigned NOT NULL,
  `auth_type` enum('PASSWORD','TOKEN','FACEBOOK','GOOGLE','TWITTER','YAHOO','MICROSOFT') NOT NULL,
  `auth_uid` varchar(255) NOT NULL,
  `auth_data` mediumtext NOT NULL,
  `auth_login_date` datetime DEFAULT NULL,
  `auth_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`auth_id`),
  UNIQUE KEY `ix_auth_type__auth_uid` (`auth_type`,`auth_uid`,`auth_deleted`),
  KEY `fk_auth_user_id` (`auth_user_id`),
  CONSTRAINT `fk_auth_user_id` FOREIGN KEY (`auth_user_id`) REFERENCES `tbl_users_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;

CREATE TABLE `tbl_users_address` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address_user_id` int(10) unsigned NOT NULL,
  `address_main` tinyint(1) unsigned NOT NULL,
  `address_firstname` varchar(50) NOT NULL,
  `address_lastname` varchar(50) NOT NULL,
  `address_society` varchar(50) DEFAULT NULL,
  `address_address` mediumtext NOT NULL,
  `address_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  UNIQUE KEY `ix_address_main_deleted` (`address_main`,`address_deleted`),
  KEY `fk_address_user_id` (`address_user_id`),
  CONSTRAINT `fk_address_user_id` FOREIGN KEY (`address_user_id`) REFERENCES `tbl_users_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;

CREATE TABLE `tbl_users_status` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status_user_id` int(11) unsigned NOT NULL,
  `status_status` enum('VERIFIED') NOT NULL,
  `status_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `ix_status_user_id__status_status` (`status_user_id`,`status_status`),
  CONSTRAINT `fk_status_user_id` FOREIGN KEY (`status_user_id`) REFERENCES `tbl_users_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_users_login` (
  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_user_id` int(10) unsigned NOT NULL,
  `login_auth_id` int(10) unsigned NOT NULL,
  `login_date` datetime NOT NULL,
  `login_ip` int(10) unsigned NOT NULL,
  PRIMARY KEY (`login_id`,`login_user_id`),
  KEY `fk_login_user_id` (`login_user_id`),
  KEY `fk_login_auth_id` (`login_auth_id`),
  CONSTRAINT `fk_login_auth_id` FOREIGN KEY (`login_auth_id`) REFERENCES `tbl_users_auth` (`auth_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_login_user_id` FOREIGN KEY (`login_user_id`) REFERENCES `tbl_users_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_commons_upload` (
  `upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upload_type` enum('USER') NOT NULL,
  `upload_external_id` int(10) unsigned NOT NULL,
  `upload_provider` enum('FS','DB') NOT NULL,
  `upload_key` enum('IMAGE','LOGO') NOT NULL,
  `upload_path` varchar(200) NOT NULL,
  `upload_name` varchar(100) NOT NULL,
  `upload_extension` varchar(10) NOT NULL,
  `upload_content_type` varchar(100) NOT NULL,
  `upload_size` int(10) unsigned NOT NULL,
  `upload_order` int(11) NOT NULL,
  `upload_date` datetime NOT NULL,
  `upload_content` longblob,
  `upload_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`upload_id`),
  KEY `ix_upload_type__external_id` (`upload_type`,`upload_external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_commons_change` (
  `change_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `change_type` enum('COMMENT','UPLOAD','ADDRESS','AUTH','LOGIN','STATUS','USER') NOT NULL,
  `change_external_id` int(10) unsigned NOT NULL,
  `change_operation` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `change_date` datetime NOT NULL,
  `change_ip` int(10) unsigned DEFAULT NULL,
  `change_user_id` int(10) unsigned DEFAULT NULL,
  `change_data` text,
  `change_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`change_id`),
  KEY `ix_change_type__change_external_id__change_operation` (`change_type`,`change_external_id`,`change_operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;

CREATE TABLE `tbl_commons_token` (
  `token_type` enum('EMAIL_CONFIRMATION') NOT NULL,
  `token_external_id` int(11) unsigned NOT NULL,
  `token_token` varchar(32) NOT NULL,
  `token_date` datetime NOT NULL,
  PRIMARY KEY (`token_type`,`token_external_id`),
  UNIQUE KEY `ix_token_type` (`token_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `tbl_commons_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_type` enum('ARTICLE','ORDER','USER') NOT NULL,
  `comment_external_id` int(10) unsigned NOT NULL,
  `comment_user_id` int(10) unsigned NOT NULL,
  `comment_comment` mediumtext,
  `comment_rating` tinyint(4) DEFAULT NULL,
  `comment_date` datetime DEFAULT NULL,
  `comment_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;
