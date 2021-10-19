<?php

namespace App\core;


class Database
{
	protected $DB_NAME;
	protected $DB_USER;
	protected $DB_PASSWORD;
	protected $DB_HOST;
	protected $DB_PORT;

		
	/**
	 * @var \PDO
	 */
	protected $db;

	public function __construct()
	{
		// Quando essa classe é instanciada, é atribuido a variável $conn a conexão com o db
		
		$this->DB_NAME = $_ENV['DB_NAME'];
		$this->DB_USER = $_ENV['DB_USERNAME'];
		$this->DB_PASSWORD = $_ENV['DB_PASSWORD'];
		$this->DB_HOST = $_ENV['DB_HOSTNAME'];
		$this->DB_PORT = $_ENV['DB_PORT'];
		
		$DSN = "mysql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_NAME}"; 
		try{
			$this->db = new \PDO($DSN,$this->DB_USER,$this->DB_PASSWORD); 
			$this->db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
		}catch(\PDOException $ex){
			die("<br>Error connecting to database: " . $ex->getMessage() . " File: " . __FILE__ . " Line: " . __LINE__ );
		}

		
	}

	/**
	 * Este método recebe um objeto com a query 'preparada' e atribui as chaves da query
	* seus respectivos valores.
	* @param  PDOStatement  $stmt   Contém a query ja 'preparada'.
	* @param  string        $key    É a mesma chave informada na query.
	* @param  string        $value  Valor de uma determinada chave.
	*/
	private function setParameters($stmt, $key, $value)
	{
		$stmt->bindParam($key, $value);
	}

	/**
	 * A responsabilidade deste método é apenas percorrer o array de com os parâmetros
	* obtendo as chaves e os valores para fornecer tais dados para setParameters().
	* @param  PDOStatement  $stmt         Contém a query ja 'preparada'.
	* @param  array         $parameters   Array associativo contendo chave e valores para fornece a query
	*/
	private function mountQuery($stmt, $parameters)
	{
		foreach( $parameters as $key => $value ) {
			$this->setParameters($stmt, $key, $value);
		}
	}

	/**
	 * Este método é responsável por receber a query e os parametros, preparar a query
	* para receber os valores dos parametros informados, chamar o método mountQuery,
	* executar a query e retornar para os métodos tratarem o resultado.
	* @param  string   $query       Instrução SQL que será executada no banco de dados.
	* @param  array    $parameters  Array associativo contendo as chaves informada na query e seus respectivos valores
	*
	* @return PDOStatement
	*/
	public function executeQuery(string $query, array $parameters = [])
	{
		$stmt = $this->conn->prepare($query);
		$this->mountQuery($stmt, $parameters);
		$stmt->execute();
		return $stmt;
	}

}