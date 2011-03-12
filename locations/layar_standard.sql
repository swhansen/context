--
--    LightRod OAR Server
    
--    Copyright (C) 2001 - 2010  High Country Software Ltd.

--    This program is free software: you can redistribute it and/or modify
--    it under the terms of the GNU Affero General Public License as
--    published by the Free Software Foundation, either version 3 of the
--    License, or (at your option) any later version.

--    This program is distributed in the hope that it will be useful,
--    but WITHOUT ANY WARRANTY without even the implied warranty of
--    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
--    GNU Affero General Public License for more details.

--    You should have received a copy of the GNU Affero General Public License
--    along with this program.  If not, see http://www.gnu.org/licenses/.



-- Usage:
--	See http://www.lightrod.org/  'LightRod API' section for the most up-to-date instructions.

-- For assistance:
--	peter AT lightrod.org
--	or the LightRod forums



CREATE TABLE tbl_layar_poi (
  int_point_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  var_type VARCHAR(255) NOT NULL,
  var_title VARCHAR(255) NOT NULL,
  var_line_2 VARCHAR(255) NOT NULL,
  var_line_3 VARCHAR(255) NOT NULL,
  var_line_4 VARCHAR(255) NOT NULL,
  var_image VARCHAR(255) NOT NULL,
  dec_latitude DECIMAL(14,10) NOT NULL,
  dec_longitude DECIMAL(15,10) NOT NULL,
  int_peano1  INTEGER UNSIGNED NOT NULL,
  int_peano2 INTEGER UNSIGNED NOT NULL,
  int_peano1iv INTEGER UNSIGNED NOT NULL,
  int_peano2iv INTEGER UNSIGNED NOT NULL,
  var_actions_label_1 VARCHAR(255),
  var_actions_uri_1 VARCHAR(255),
  var_address VARCHAR(1000) NULL,
  
  PRIMARY KEY(int_point_id),
  INDEX peano1 (int_peano1, int_point_id),
  INDEX peano2 (int_peano2, int_point_id),
  INDEX peano1iv (int_peano1iv, int_point_id),
  INDEX peano2iv (int_peano2iv, int_point_id)

);

INSERT INTO tbl_layar_poi(  int_point_id,
  var_type,
  var_title,
  var_line_2,
  var_line_3,
  var_line_4,
  var_image,
  dec_latitude,
  dec_longitude,
  int_peano1,
  int_peano2,
  int_peano1iv,
  int_peano2iv,
  var_actions_label_1,
  var_actions_uri_1)
  VALUES(0, "","Hello World","Line 2", "Line 3", "Line 4", "http://www.lightrod.org/light_rod_logo.png", 50,0,2640303607, 3273185755, 1654663688, 1021781540, "Click Here", "http://www.lightrod.org");


