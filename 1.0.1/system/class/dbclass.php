<?php
/*
*	@author : Ahsan Zahid Chowdhury
*	@owner : Exception Solutions
*	@email : azc.pavel@gmail.com
*	@phone : +880 1677 533818
*	@since : 2014-04-20
*	@version : 1.0.1
*	DB Class
*/

Final class DbClass
{	
	private $select 	= NULL;	
	private $table;
	private $join 		= '';
	private $where 		= 1;
	private $order_by 	= '';
	private $group_by 	= '';
	private $limit 		= '';
	private $query;
	private $query_str;
	private $error_query;
	private $affected_rows;
	private $db_driver;
	
	// Public Property
	public $db_host;
	public $db_uname;
	public $db_pass;
	public $db_name;
	public $db_prefix;
	public $db_service;
	public $db_protocol;
	public $db_server;
	public $db_uid;
	public $db_options;

	
	function __construct($driver = '',$host = '',$user = '',$pass = '',$db = '',$dbPrefix = '',$port = '',$service = '',$protocol = '',$server = '',$uid = '',$options = '')
	{
		if($driver == 'mysql' || $driver == 'mysqli')
			$dsn = "mysql:host=$host;port=$port;dbname=$db";

		elseif($driver == 'cubrid')
			$dsn = "cubrid:dbname=$db;host=$host;port=$post";

		elseif($driver == 'firebird')
			$dsn = "firebird:dbname=$host/port:$port$db";

		elseif($driver == 'ibm')
			$dsn = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$db;HOSTNAME=$host;PORT=$port;PROTOCOL=$protocol;";

		elseif($driver == 'informix')
			$dsn = "informix:host=$host;service=$service;database=$db;server=$server;protocol=$protocol;EnableScrollableCursors=1";

		elseif($driver == 'oci')
			$dsn = "oci:dbname=//$host:$port/$db";

		elseif($driver == 'sqlsrv')
			$dsn = "sqlsrv:Server=$host,$port;Database=$db";

		elseif($driver == 'odbc')
			{
				if (!file_exists($db)) {
				    die("Could not find database file in $db");
				}

				$dsn = "odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)}; DBQ=$db; Uid=$user; Pwd=$pass;";
			}

		elseif($driver == 'pgsql')
			$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

		elseif($driver == '4D')
			$dsn = "4D:host=$host;charset=UTF-8";


		try{

			$this->db_driver 	= $driver;
			$this->db_host 		= $host;
			$this->db_uname 	= $user;
			$this->db_pass 		= $pass;
			$this->db_name 		= $db;
			$this->db_prefix 	= $dbPrefix;
			$this->db_host 		= $host;
			$this->db_port 		= $port;
			$this->db_service 	= $service;
			$this->db_protocol 	= $protocol;
			$this->db_server 	= $server;
			$this->db_uid 		= $uid;
			$this->db_options	= $options;

			if(is_array($options) && count($options) > 0)
				$this->pdo = @new pdo($dsn,$user,$pass,$this->db_options);
			else
				$this->pdo = @new pdo($dsn,$user,$pass);
			
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$this->pdo->beginTransaction();

		} catch (PDOException $error) {
		    $this->printError($error);
		}
		
	}

	function __call($mth_name,$mth_arg)
	{
		echo "Unknown Member Call $mth_name<br>You can get all details by calling get_class_details() method";
	}

	private function printError($error)
	{
		echo "Query Error:<br><br>".$error->getMessage()."<br><br>";
		echo "Query Trace:<br><br>";
		foreach ($error->getTrace() as $key => $value) {
			echo 'In file "'.$value['file'].'"<br>';
			echo 'In line "'.$value['line'].'"<br>';
			echo 'In function "'.$value['function'].'"<br>';
			echo (isset($value['class'])) ? 'In class "'.$value['class'].'"<br>' : '';
			echo (isset($value['type'])) ? 'In type "'.$value['type'].'"<br>' : '';
			foreach ($value['args'] as $argsKey => $argsValue) {
				if(is_array($argsValue))
					foreach ($argsValue as $argsKeySub => $argsValueSub) {
						echo (strlen($argsValueSub) > 0) ? 'In args "'.$argsKeySub." => ".$argsValueSub.'"<br>' : '';	
					}
				else
					echo (strlen($argsValue) > 0) ? 'In args "'.$argsValue.'"<br>' : '';	
			}
			echo "<br>";
		}
		die();
	}

	function get_class_details()
	{		
		echo '<pre>';
		echo "<br><b>Class Name</b><br>";
		echo "\t".get_class($this);

		echo "<br><br><b>List of Methods</b><br>";
		foreach (get_class_methods($this) as $key => $value) {
			echo "\t".$value."()<br>";
		}		
		
		echo "<br><b>List of Properties</b><br>";
		if(count(get_object_vars($this)) > 0)
			print_r($this);
		else
			echo "\t"."No Properties Exists";
		
		exit;
	}

	function query($query = "")
	{
		$select = ($this->select == NULL ) ? '*' : $this->select;
		if ($query === "")
			$query = "SELECT {$select} FROM {$this->db_prefix}{$this->table} {$this->join} WHERE {$this->where} {$this->order_by} {$this->group_by} {$this->limit}";

		$this->query_str = $query;
		try{			
			$this->query = $this->pdo->query($query) or $error_t = $this->pdo->errorInfo();			
		}
		catch(PDOException $error){
			$this->printError($error);
		}		
		
		$this->order_by = $this->join = $this->limit = '';
		$this->where = 1;
		$this->select = NULL;
		
		return $this;
	}

	function get($table = 0)
	{
		$this->table = (($table !== 0) ? $table : $this->table);
		
		if(count(func_get_args()) > 1 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(1));
		if(count(func_get_args()) > 2 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(1).", ".func_get_arg(2))."";		

		$this->query();
		
		return $this;
	}

	function get_where($table = 0, $where = 1)
	{
		$this->table = (($table !== 0) ? $table : $this->table);

		if(count(func_get_args()) > 2 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(2));
		if(count(func_get_args()) > 3 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(2)).", ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(3))."";		

		$this->where($where);

		$this->query();

		return $this;
	}

	function select($select = NULL)
	{
		if($this->select != NULL)
			$this->select .= ',';
		$this->select .= $select;

		return $this;
	}

	function select_max($MAX, $as = 0)
	{
		$this->select = ($as === 0 ) ? "MAX($MAX) as $MAX" : "MAX($MAX) as $as";

		return $this;
	}

	function select_MIN($MIN, $as = 0)
	{
		$this->select = ($as === 0 ) ? "MIN($MIN) as $MIN" : "MIN($MIN) as $as";

		return $this;
	}

	function select_AVG($AVG, $as = 0)
	{
		$this->select = ($as === 0 ) ? "AVG($AVG) as $AVG" : "AVG($AVG) as $as";

		return $this;
	}	

	function from($table)
	{
		$this->table = $table;

		return $this;
	}

	function join($table, $join, $pos = 0)
	{
		$join = explode('=', $join);
		foreach ($join as $key => $value) {
			$join[$key] = $this->db_prefix.trim($value);
		}
		$join = implode('=', $join);
		$this->join .=  (($pos !== 0) ? " ".strtoupper($pos)." JOIN {$this->db_prefix}$table ON $join " : "JOIN {$this->db_prefix}$table ON $join ");

		return $this;
	}

	function group_by($group_by = '')
	{
		$this->group_by = 'GROUP BY '.$group_by;

		return $this;
	}

	function where($where = 1, $isSec = false)
	{
		$where_full = '';
		$db_prefix 	= '';
		if(is_array($where)){
			if(isset($where['db_prefix']))
			{
				$db_prefix = $this->db_prefix;
			}
			foreach ($where as $key => $value) {

				if( preg_match('/<|>|\=|!| LIKE| BETWEEN| IN| NOT IN/', $key) && (preg_match('/ AND$| OR$|\'|^\(|\)$/', $value)) )					
					$where_full .= " {$db_prefix}$key $value";
				elseif(preg_match('/<|>|\=|!/', $key))
					$where_full .= " {$db_prefix}$key '$value' AND";
				elseif (preg_match('/ AND$| OR$|^\(|\)$/', $value))
					$where_full .= " {$db_prefix}$key = $value";
				else
					$where_full .= " {$db_prefix}$key = '$value' AND";
			}
			
			if(substr($where_full,-4) === ' AND'){
				if($this->where != 1)
					$this->where .= substr($where_full, 0, -4);
				else
					$this->where = substr($where_full, 0, -4);
			}
			else{
				if($this->where != 1)
					$this->where .= ' AND '.$where_full;
				else
					$this->where = $where_full;
			}
		}
		elseif($this->where != 1 AND $isSec != false)
			$this->where .= " AND $where = '$isSec'";		
		elseif($this->where != 1 AND $isSec == false)
			$this->where .= " AND $where";
		elseif($this->where == 1 AND $isSec != false)
			$this->where = "$where = '$isSec'";
		else
			$this->where = $where;

		return $this;
	}

	function order_by($order_by = 0, $order = 0)
	{
		
			if($this->order_by == '')
				$this->order_by = "ORDER BY $order_by";
			else
				$this->order_by .= " ,$order_by";
		
			$this->order_by .= " $order";

		return $this;
	}

	function limit()
	{
		if(count(func_get_args()) > 1 )
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(0)).", ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(1))."";
		else
			$this->limit = "LIMIT ".preg_replace(array('/[A-Z]*[a-z]*[ ]*/','[_()]','<\W+>'), array('','',''), func_get_arg(0));

		return $this;
	}

	function num_rows()
	{
		$num = $this->pdo->query($this->query_str) or $error_t = $this->pdo->errorInfo();

		if(isset($error_t) && $error_t[1] != '')
		{
			exit('Error No: '.$error_t[1].'<br>Error Co: '.$error_t[2]."<br> $query");
		}
		$num = count($num->fetchAll());
		return $num;
	}


	function row_array($index = NULL)
	{		
		if($index)
		{
			$result = $this->result_array();
			if(isset($return[$index]))
			return $return[$index];
		}
		return $this->query->fetch(PDO::FETCH_ASSOC);
	}

	function result_array()
	{		
		while($result[] = $this->query->fetch(PDO::FETCH_ASSOC)){}
			array_pop($result);

		return $result;
	}

	function row($index = NULL)
	{		
		if($index)
		{
			$result = $this->result();
			if(isset($return[$index]))
			return $return[$index];
		}
		return $this->query->fetch(PDO::FETCH_OBJ);
	}

	function result()
	{			
		while($result[] = $this->query->fetch(PDO::FETCH_OBJ)){}
			array_pop($result);

		return $result;		
	}

	function fetch_column($limit = 0)
	{
		return $this->query->fetchColumn($limit);
	}

	function show_column($table)
	{
		return $this->query("SHOW COLUMNS FROM {$this->db_prefix}$table");
	}

	function show_tables()
	{
		return $this->query("SHOW TABLES FROM {$this->db_name}");
	}


	function insert($table, $values = 1, $typeQr = 'INSERT')
	{

		$values_full = '(';
		$key_full = '';
		
		if(is_array($values)){
			$key_full = '(';
			foreach ($values as $key => $value) {
				
					$values_full .= "'$value',";
					$key_full .= "$key,";
			}

			$values_full = substr($values_full, 0, -1).')';
			$key_full = substr($key_full, 0, -1).')';
		}
		else
			$values_full .= $values.')';
	
		try{
			$this->affected_rows = $this->pdo->exec("{$typeQr} INTO {$this->db_prefix}{$table} $key_full VALUES {$values_full}"); 	
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}		

		return $this;

	}

	function update($table, $values = '', $where = false)
	{		
		$values_full = '';

		if($where != false)
			$this->where($where);

		if(is_array($values)){
			foreach ($values as $key => $value) {
				
					$values_full .= " $key = '$value',";
			}

			$values_full = substr($values_full, 0, -1);
		}
		else
			$values_full = $values;

		try{
			$this->affected_rows = $this->pdo->exec("UPDATE {$this->db_prefix}$table SET$values_full WHERE {$this->where} ");
		}		
		catch (PDOException $error)
		{
			$this->printError($error);
		}

		$this->where = 1;

		return $this;
	}


	function delete($table, $where = 1)
	{		

		$this->where($where);

		try{
			$this->affected_rows = $this->pdo->exec("DELETE FROM {$this->db_prefix}$table WHERE {$this->where}");
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}

		$this->where = 1;
		
		return $this;
		
	}

	function affected_rows()
	{
		return $this->affected_rows;
	}

	function insert_id()
	{
		return $this->pdo->lastInsertId();
	}

	function last_row($parm = 'obj')
	{
		$last = ($parm != 'obj') ? $this->result_array() : $this->result();
		if (count($last) > 0) {			
			return $last[count($last)-1];
		}
	}

	function optimaze_table($table)
	{
		$this->query("OPTIMIZE TABLE {$this->db_prefix}$table ");
		return $this;
	}

	function truncate_table($table)
	{
		try{
			$this->affected_rows = $this->pdo->exec("TRUNCATE TABLE {$this->db_prefix}$table ");
		}
		catch (PDOException $error)
		{
			$this->printError($error);
		}

		return $this;
	}

	function drop_table($table)
	{
		$this->query("DROP TABLE $table ");
		return $this;
	}	

	function __destruct()
	{
		$this->pdo->commit();
		$this->pdo = null;
	}
}

?>
