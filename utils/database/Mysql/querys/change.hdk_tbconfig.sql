
-- Verifica duplicidades --
SELECT
   GROUP_CONCAT(idconfig),
   session_name,
   COUNT(*) c
FROM
   hdk_tbconfig
GROUP BY session_name
HAVING c > 1 ;

-- Excluir as duplicidades --
DELETE FROM hdk_tbconfig WHERE idconfig IN (........)

-- Criar o index --
CREATE UNIQUE INDEX hdk_tbconfig_session_idx
ON hdk_tbconfig (session_name)