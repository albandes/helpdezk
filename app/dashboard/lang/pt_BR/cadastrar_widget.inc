<?php
$l_ate = array("login_ja_existe"                        => "Login j� existe, volte e informe outro login.",
               "codOp_ja_existe"                        => "C�digo Operador j� existe, volte e informe outro c�digo.",
               "titulo"                                 => "Atendente - Detalhes",
               "lb_nome"                                => "Nome",
               "lb_email"                               => "E-mail",
               "lb_telefone"                            => "Telefone",
               "lb_ramal"                               => "Ramal",
               "lb_celular"                             => "Celular",
               "lb_cod_operador"                        => "Cod. Operador",
               "lb_login"                               => "Login",
               "lb_modo_login"                          => "Modo Login",
               "opt_HelpDesk"                           => "HelpDesk",
               "opt_AD"                                 => "Active Directory",
               "opt_POP"                                => "POP",
               "lb_senha"                               => "Senha",
               "lb_repita_senha"                        => "Repita Senha",
               "lb_empresa"                             => "Empresa",
               "lb_departamento"                        => "Departamento",
               "lb_centro_custo"                        => "Centro de Custo",
               "lb_nivel_acesso"                        => "N�vel de Acesso",
               "lb_grupo_permissoes"                    => "Grupo de Permiss�es",
               "lb_usuario_vip"                         => "Usu�rio Vip",
               "lb_usuario_ativo"                       => "Usu�rio Ativo",
               "lb_valor_hora"                          => "Valor Hora",
               "lb_hora_extra"                          => "Hora Extra",
               "btn_alterar"                            => "Alterar",
               "btn_cancelar"                           => "Cancelar"
);

array_htmlentities($l_ate);

$l_ate_msg = array("informe_nm_usuario"                 => "Login j� existe, volte e informe outro login.",
                   "informe_login"                      => "Informe o Login",
                   "login_ja_existe"                    => "Informe o Login",
                   "informe_senha"                      => "Informe a senha.",
                   "defina_grupo_permissao"             => "Defina um grupo de permiss�o.",
                   "informe_email"                      => "Informe o e-mail.",
                   "informe_depto"                      => "Informe o departamento."
);

$l_ate_alt = array("cadastrar_empresa"                  => "Cadastrar Empresa",
                   "cadastrar_depto"                    => "Cadastrar Departamento",
);

$l_ate["msg"] = $l_ate_msg;
$l_ate["alt"] = $l_ate_alt;
?>