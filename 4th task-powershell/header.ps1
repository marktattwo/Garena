$content = ""
$d= Get-Date
$dString = $d.Year.ToString() + "-" + $d.Month.ToString() + "-" + $d.Day.ToString() + " " + $d.Hour.ToString() + ":" + $d.Minute.ToString() + ":" + $d.Minute.ToString()
$content += "/*Date : $dString*/"
$content += "

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ``cafe_tab``
-- ----------------------------
DROP TABLE IF EXISTS ``cafe_tab``;
CREATE TABLE ``cafe_tab`` (
  ``id`` int(11) NOT NULL AUTO_INCREMENT,
  ``name`` varchar(255) NOT NULL,
  ``address`` varchar(255) NOT NULL,
  ``location`` varchar(255) NOT NULL,
  ``owner_id`` int(11) DEFAULT NULL,
  ``cybercafe_id`` varchar(255) NOT NULL,
  ``date_created`` datetime NOT NULL,
  ``is_active`` tinyint(1) NOT NULL DEFAULT '1',
  ``release_version`` int(10) unsigned NOT NULL DEFAULT '0',
  ``area`` varchar(255) NOT NULL DEFAULT '',
  ``business`` int(11) NOT NULL DEFAULT '0',
  ``latitude`` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  ``longitude`` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  ``business_type`` int(11) DEFAULT NULL,
  ``cafe_remark`` varchar(255) NOT NULL DEFAULT '',
  ``post_code`` varchar(5) NOT NULL DEFAULT '',
  ``selected_for_default_app_seller`` tinyint(4) DEFAULT NULL,
  ``is_address_verified`` tinyint(1) NOT NULL DEFAULT '0',
  ``cafe_opening_time`` time NOT NULL DEFAULT '00:00:00',
  ``cafe_closing_time`` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (``id``),
  UNIQUE KEY ``cybercafe_id`` (``cybercafe_id``),
  UNIQUE KEY ``selected_for_default_app_seller`` (``selected_for_default_app_seller``,``owner_id``),
  KEY ``cafe_tab_5d52dd10`` (``owner_id``),
  KEY ``area`` (``area``),
  KEY ``location`` (``location``),
  CONSTRAINT ``____owner_id_refs_id_c22f87a7`` FOREIGN KEY (``owner_id``) REFERENCES ``retailer_tab`` (``id``)
) ENGINE=InnoDB AUTO_INCREMENT=18273 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cafe_tab
-- ----------------------------
"


Set-Content -Encoding UTF8 -Path C:\temp\header.txt -Value $content
