/*
 *      Procedure Name  :  spm_insertAtleta
 *      Database/Schema :  spm
 *
 *      Description:
 *          Insere um atleta no tbperson e nas tabelas especificas do modulo SPM
 *
 *      Tables Impacted :
 *          tb_person
 *          
 *
 *      Params:
 *          IN:  typelogin_ID      - ID tipo de login 
 *          IN:  typeperson_ID     - ID do tipo de pessoa
 *          IN:  nature_ID         - ID da natureza da pessoa
 *          IN:  theme_ID          - ID do tema de tela
 *          IN:  nome              - Nome da pessoa
 *          IN:  login             - Login da Pessoa 
 *          IN:  email             - Email da pessoa
 *          IN:  senha             - Senha da pessoa
 *          IN:  status            - Status da pessoa
 *          IN:  user_vip          - Usuario vip 
 *          IN:  telefone          - Telefone da pessoa
 *          IN:  ramal             - Ramal da pessoa
 *          IN:  celular           - Celular da Pessoa
 *          IN:  location_ID       - ID da localizacao da pessoa
 *          IN:  time_value        - Valor da hora da pessoa
 *          IN:  overtime          - Valor da hora extra da pessoa
 *          IN:  change_pass       - Pessoa deve troca senha no primeiro login
 *
 *          IN:  condicao_ID       - ID da condicao medica da pessoa
 *          IN:  departamento_ID   - ID do departamento da pessoa
 *          IN:  posicao_ID        - ID da posicao que joga a pessoa 
 *          IN:  apelido           - Apelido da pessoa  
 *
 *          OUT: person_ID         - ID da pessoa incluida
 *
 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2018/01/23     albandes    Original
 *
 */
DELIMITER $$

DROP PROCEDURE IF EXISTS `spm_insertAtleta`$$

CREATE PROCEDURE `spm_insertAtleta`(
           IN  typelogin_ID     INT,
           IN  typeperson_ID    INT,
           IN  nature_ID        INT,
           IN  theme_ID         INT,
           IN  nome             VARCHAR(60),
           IN  login            VARCHAR(45),
           IN  email            VARCHAR(45),
           IN  senha            VARCHAR(45),
           IN  status           CHAR(1),
           IN  user_vip         CHAR(1),
           IN  telefone         VARCHAR(30),
           IN  ramal            CHAR(10),
           IN  celular          VARCHAR(30),
           IN  location_ID      INT,
           IN  time_value       float,
           IN  overtime         float,
           IN  change_pass      int(1),
           IN  condicao_ID      INT,
           IN  departamento_ID  INT,
           IN  posicao_ID       INT,
           IN  apelido          VARCHAR(100),
           OUT person_ID        INT
)
BEGIN
    START TRANSACTION;

    INSERT INTO tbperson
    (
      idtypelogin
     ,idtypeperson
     ,idnatureperson
     ,idtheme
     ,name
     ,login
     ,password
     ,email
     ,dtcreate
     ,status
     ,user_vip
     ,phone_number
     ,cel_phone
     ,branch_number
     ,fax
     ,cod_location
     ,time_value
     ,overtime
     ,change_pass
     ,token
    )
    VALUES
    (
      typelogin_ID  -- idtypelogin - INT(11) NOT NULL
     ,typeperson_ID -- idtypeperson - INT(11) NOT NULL
     ,nature_ID     -- idnatureperson - INT(11) NOT NULL
     ,theme_ID      -- idtheme - INT(11) NOT NULL
     ,nome          -- name - VARCHAR(60) NOT NULL
     ,login         -- login - VARCHAR(45)
     ,senha         -- password - VARCHAR(45)
     ,email         -- email - VARCHAR(45)
     ,NOW()         -- dtcreate - DATETIME
     ,status        -- status - CHAR(1) NOT NULL
     ,user_vip      -- user_vip - CHAR(1) NOT NULL
     ,telefone      -- phone_number - VARCHAR(30)
     ,celular       -- cel_phone - VARCHAR(30)
     ,ramal         -- branch_number - VARCHAR(10)
     ,''            -- fax - VARCHAR(30)
     ,location_ID   -- cod_location - INT(4)
     ,time_value    -- time_value - FLOAT
     ,overtime      -- overtime - FLOAT
     ,change_pass   -- change_pass - INT(1)
     ,''            -- token - VARCHAR(45)
    );
   
    set person_ID := LAST_INSERT_ID();

    INSERT INTO spm_person_condicao
    (
      idperson
     ,idcondicao
    )
    VALUES
    (
      person_ID     -- idperson - INT(11) NOT NULL
     ,condicao_ID   -- idcondicao - INT(11) NOT NULL
    );

    INSERT INTO spm_person_departamento
    (
      idperson
     ,iddepartamento
    )
    VALUES
    (
      person_ID       -- idperson - INT(11) NOT NULL
     ,departamento_ID -- iddepartamento - INT(11) NOT NULL
    );

    INSERT INTO spm_person_posicao
    (
      idperson
     ,idposicao
    )
    VALUES
    (
      person_ID   -- idperson - INT(11) NOT NULL
     ,posicao_ID  -- idposicao - INT(11) NOT NULL
    );

  INSERT INTO soccer.spm_tbapelido
  (
    idperson
   ,nome
  )
  VALUES
  (
    person_ID   -- idperson - INT(11) NOT NULL
   ,apelido     -- nome - VARCHAR(100) NOT NULL
  );
       
 COMMIT;

END$$

DELIMITER ;