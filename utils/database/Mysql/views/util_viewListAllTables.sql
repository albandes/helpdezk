DELIMITER $$

DROP VIEW IF EXISTS `util_viewListAllTables`$$

CREATE VIEW `util_viewListAllTables` AS 

SELECT 
  table_name AS "Table",
  ROUND(((data_length + index_length) / 1024 / 1024),2) "Total Size (MB)",
  ROUND(((data_length) / 1024 / 1024), 2) "Data Size (MB)",
  ROUND(((index_length) / 1024 / 1024), 2) "Index Size (MB)",
  TABLE_ROWS "Rows",
  ENGINE,
  AUTO_INCREMENT "Auto Increment" ,
  table_schema 
FROM
  information_schema.TABLES 

ORDER BY (data_length + index_length) DESC$$

DELIMITER ;
