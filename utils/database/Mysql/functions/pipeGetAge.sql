DROP FUNCTION IF EXISTS pipeGetAge;

DELIMITER |

CREATE FUNCTION pipeGetAge( data_nascimento DATETIME )
RETURNS INT
BEGIN
    DECLARE idade INT;
    DECLARE ano_atual INT;
    DECLARE mes_atual INT;
    DECLARE dia_atual INT;
    DECLARE ano INT;
    DECLARE mes INT;
    DECLARE dia INT;

    SET ano_atual = YEAR(CURDATE());
    SET mes_atual = MONTH( CURDATE());
    SET dia_atual = DAY( CURDATE());

    SET ano = YEAR( data_nascimento );
    SET mes = MONTH( data_nascimento );
    SET dia = DAY( data_nascimento );

    SET idade = ano_atual - ano;

    IF( mes > mes_atual ) THEN
            SET idade = idade - 1;
    END IF;

    IF( mes = mes_atual AND dia > dia_atual ) THEN
            SET idade = idade - 1;
    END IF;

    RETURN idade;
END|

DELIMITER ;