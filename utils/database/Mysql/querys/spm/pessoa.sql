SELECT
  tbtypestreet.idtypestreet,
  tbtypestreet.name tipologradouro,
  tbtypestreet.abbr tipologradouro_abbr,
  tbstreet.idstreet,
  tbstreet.name logradouro_nome,
  tbaddress.idaddress,
  tbaddress.number,
  tbaddress.complement,
  tbaddress.zipcode,
  tbcity.idcity,
  tbcity.name cidade,
  tbstate.idstate,
  tbstate.abbr,
  tbstate.name estado,
  tbcountry.idcountry,
  tbcountry.iso,
  tbcountry.name pais,
  tbcountry.iso3,
  tbcountry.printablename,
  tbneighborhood.idneighborhood,
  tbneighborhood.name bairro,
  tbnaturalperson.idnaturalperson,
  tbnaturalperson.ssn_cpf,
  tbnaturalperson.rg,
  tbnaturalperson.rgoexp,
  tbnaturalperson.dtbirth,
  tbnaturalperson.mother,
  tbnaturalperson.father,
  tbnaturalperson.gender,
  tbperson.idperson,
  tbperson.idtheme,
  tbperson.name nome,
  tbperson.login,
  tbperson.password,
  tbperson.email,
  tbperson.dtcreate,
  tbperson.status,
  tbperson.phone_number,
  tbperson.cel_phone,
  tbperson.branch_number,
  tbperson.fax,
  tbperson.change_pass,
  spm_tbcondicao.idcondicao,
  spm_tbcondicao.nome condicao,
  spm_tbcondicao.cor,
  spm_tbposicao.idposicao,
  spm_tbposicao.nome posicao,
  spm_tbdepartamento.iddepartamento,
  spm_tbdepartamento.nome departmento
FROM tbperson
  INNER JOIN tbtypeperson
    ON tbperson.idtypeperson = tbtypeperson.idtypeperson
  INNER JOIN tbaddress
    ON tbaddress.idperson = tbperson.idperson
  INNER JOIN tbcity
    ON tbaddress.idcity = tbcity.idcity
  INNER JOIN tbstate
    ON tbcity.idstate = tbstate.idstate
  INNER JOIN tbcountry
    ON tbstate.idcountry = tbcountry.idcountry
  INNER JOIN tbstreet
    ON tbaddress.idstreet = tbstreet.idstreet
  INNER JOIN tbneighborhood
    ON tbaddress.idneighborhood = tbneighborhood.idneighborhood AND tbneighborhood.idcity = tbcity.idcity
  INNER JOIN tbtypeaddress
    ON tbaddress.idtypeaddress = tbtypeaddress.idtypeaddress
  INNER JOIN tbtypestreet
    ON tbstreet.idtypestreet = tbtypestreet.idtypestreet
  INNER JOIN tbnaturalperson
    ON tbnaturalperson.idperson = tbperson.idperson
  INNER JOIN spm_person_condicao
    ON spm_person_condicao.idperson = tbperson.idperson
  INNER JOIN spm_person_posicao
    ON spm_person_posicao.idperson = tbperson.idperson
  INNER JOIN spm_tbcondicao
    ON spm_person_condicao.idcondicao = spm_tbcondicao.idcondicao
  INNER JOIN spm_tbposicao
    ON spm_person_posicao.idposicao = spm_tbposicao.idposicao
  INNER JOIN spm_person_departamento
      ON spm_person_departamento.idperson = tbperson.idperson
   INNER JOIN spm_tbdepartamento
      ON spm_person_departamento.iddepartamento = spm_tbdepartamento.iddepartamento