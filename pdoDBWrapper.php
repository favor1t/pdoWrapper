<?
class pdoDBWrapepr extends PDO{

	public function __construct($dsn, $user="", $psw=""){
		$options = array(
			PDO::ATTR_PERSISTENT => true, 
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		try {
			parent::__construct($dsn, $user, $psw,$options);
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function insert($table, $fields){
		$sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
		$bind = array();
		return $this->run($sql, $bind);
	}

	public function select($table, $where="",$bind=array(),$fields="*"){
		$sql = "SELECT " . $fields . " FROM " . $table;
		if(!empty($where)){
			$sql .= " WHERE " . $where;
		}
		$sql .= ";";
		return $this->run($sql,$bind);
	}
	public function update($table,$wher,$bind,$fields){
		$sql = "UPDATE " . $table . " SET ";
		for($f = 0; $f < $fieldSize; ++$f) {
			if($f > 0)
				$sql .= ", ";
			$sql .= $fields[$f] . " = :update_" . $fields[$f]; 
		}
		$sql .= " WHERE " . $where . ";";

		foreach($fields as $field)
			$bind[":update_$field"] = $info[$field];
		
		return $this->run($sql, $bind);
	}
	public function delete($table, $where,$bind){
		$sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
		$this->run($sql, $bind);
	}

	public function sqlRun($sql){
		$sth = $this->prepare($sql);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	private function run($sql,$bind){
		try{
			$sth = $this->prepare($sql);
			if($sth->execute($bind) !== false) {
				if(preg_match("/^(" . implode("|", array("select")) . ") /i", $sql)){
					return $sth->fetchAll(PDO::FETCH_ASSOC);
				}elseif(preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $sql)){
					return $sth->rowCount();
				}
			}	
		}catch(PDOException $e){
			return $e->getMessage();
		}
	}

} 
?>