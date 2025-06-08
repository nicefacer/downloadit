<?php

class ML_Database_Model_Db {
	protected static $instance = null;
	protected $destructed = false;
	
	protected $driver = null; // instanceof mysqli or mysql driver
	
	protected $access = array (
		'host' => '',
		'user' => '',
		'pass' => '',
		'persistent' => false,
	);
	protected $database = '';
	
	protected $query = '';
	protected $error = '';
	protected $result = null;
	
	protected $sqlErrors = array();
	
	protected $start = 0;
	protected $count = 0;
	protected $querytime = 0;
	protected $doLogQueryTimes = true;
	protected $timePerQuery = array();

	protected $availabeTables = array();

	protected $escapeStrings = false;

	protected $sessionLifetime;
	
	protected $showDebugOutput = false;
	
	/* Caches */
	protected $columnExistsInTableCache = array();

	/**
	 * Class constructor
	 */
	protected function __construct() { 
		$this->start         = microtime(true);
		$this->count         = 0;
		$this->querytime     = 0;
                        $this->showDebugOutput = MLSetting::gi()->get('blDebug');
		// magic quotes are deprecated as of php 5.4
        // v3 unescape magic quotes in shop_http class
		$this->escapeStrings = false;//get_magic_quotes_gpc();
		
                        $aDbConnection = MLShop::gi()->getDbConnection();
		$this->access['host'] = $aDbConnection['host'];
		$this->access['user'] = $aDbConnection['user'];
		$this->access['pass'] = $aDbConnection['password'];
                        if(isset($aDbConnection['port'])){//for some server that you have socket and port
                            $this->access['port'] = $aDbConnection['port'];
                        }
		$this->access['persistent'] = (isset($aDbConnection['persistent']) && $aDbConnection['persistent']  );		
		$this->database = $aDbConnection['database'];
                
		$driverClass = $this->selectDriver();
		$this->driver = new $driverClass($this->access);
		
		
		$this->timePerQuery[] = array (
			'query' => 'Driver: "'.get_class($this->driver).'" ('.$this->getDriverDetails().')',
            'error' => false,
			'time' => 0
		);
		
		$this->selfConnect(false, true);
		if (defined('MAGNADB_ENABLE_LOGGING') && MAGNADB_ENABLE_LOGGING) {
			$dbt = @debug_backtrace();
			if (!empty($dbt)) {
				foreach ($dbt as $step) {
					if (strpos($step['file'], 'magnaCallback') !== false) {
						$dbt = true;
						unset($step);
						break;
					}
				}
			}
			if ($dbt !== true) {
                                        MLLog::gi()->add('db_query', "### Query Log ".date("Y-m-d H:i:s")." ###\n\n");
			}
			unset($dbt);
		}
		
		$this->reloadTables();
		
//		$this->initSession();
	}
	
    protected function selectDriver() {
        // we prefer mysqli only for php 5.3 or greater as this version introduces persistent connections
        if (function_exists('mysqli_query') && defined('PHP_VERSION_ID') && (PHP_VERSION_ID >= 50300)) {            
            return MLFilesystem::gi()->loadClass("model_db_mysqli");
        } else {            
            return MLFilesystem::gi()->loadClass("model_db_mysql");
        }
    }

    protected function getDriverDetails() {
		$data = $this->driver->getDriverDetails();
		$info = '';
		foreach ($data as $key => $value) {
			$info .= '"'.$key.'": "'.$value.'",   ';
		}
		$info = rtrim($info, ', ').'';
		return $info;
	}
	
	/**
	 * @return ML_Database_Model_Db Singleton - gets Instance
	 */
	public static function gi() {
		if (self::$instance == NULL) {
			self::$instance = new self();
            MLShop::gi()->initializeDatabase();
		}
		return self::$instance;
	}

	protected function __clone() {}
	
	public function __destruct() {
		if (!is_object($this) || !isset($this->destructed) || $this->destructed) {
			return;
		}
		$this->destructed = true;
		
		if (!defined('MAGNALISTER_PASSPHRASE') && !defined('MAGNALISTER_PLUGIN')) {
			/* Only when this class is instantiated from magnaCallback
			   and the plugin isn't activated yet.
			*/
			$this->closeConnection();
			return;
		}
		
//		$this->sessionRefresh();
		
		if (MLSetting::gi()->get('blDebug') && $this->showDebugOutput && function_exists('microtime2human') 
			&& (
				!defined('MAGNA_CALLBACK_MODE') || (MAGNA_CALLBACK_MODE != 'UTILITY')
			) && (stripos($_SERVER['PHP_SELF'].serialize($_GET), 'ajax') === false)
		) {
			echo '<!-- Final Stats :: QC:'.$this->getQueryCount().'; QT:'.microtime2human($this->getRealQueryTime()).'; -->';
		}
		$this->closeConnection();
	}
	
	public function selectDatabase($db) {
		$this->query('USE `'.$db.'`');
	}
	
	protected function isConnected() {
		return $this->driver->isConnected();
	}
	
	protected function selfConnect($forceReconnect = false, $initialConnect = false) {
		# Wenn keine Verbindung im klassischen Sinne besteht, selbst eine herstellen.
		if ($this->driver->isConnected() && !$forceReconnect) {
			return false;
		}
		
		$this->driver->connect();
		
		if (!$initialConnect
			&& isset($_GET['MLDEBUG']) && ($_GET['MLDEBUG'] === 'true')
			&& isset($_GET['LEVEL'])   && (strtolower($_GET['LEVEL']) == 'high')
		) {
			echo "\n<<<< ML_Database_Model_Db :: reconnect >>>>\n";
		}
		
		if (!$this->isConnected()) {
			// called in the destructor: Just leave. No need to close connection, it's lost
			if ($this->destructed) exit;
			// die is bad behaviour. But meh...
			die(
				'<span style="color:#000000;font-weight:bold;">
					<small style="color:#ff0000;font-weight:bold;">[SQL Error]</small><br>
					Establishing a connection to the database failed.<br><br>
					<pre style="font-weight:normal">'.htmlspecialchars(
						print_r(array_slice(debug_backtrace(true), 4), true)
					).'</pre>
				</span>'
			);
		}
		$vers = $this->driver->getServerInfo();
		if (substr($vers, 0, 1) > 4) {
			$this->query("SET SESSION sql_mode=''");
		}
		$this->selectDatabase($this->database);
		
		return true;
	}
	
	protected function closeConnection($force = false) {
		if (   $force
			|| ($this->isConnected() && !(defined('USE_PCONNECT') && (strtolower(USE_PCONNECT) == 'true')))
		) {
			if (is_object($this->driver)) {
				$this->driver->close();
			}
		}
	}
	
	protected function prepareError() {
		$errNo = $this->driver->getLastErrorNumber();
		if ($errNo == 0) {
			return '';
		}
		return $this->driver->getLastErrorMessage().' ('.$errNo.')';
	}

	public function logQueryTimes($b) {
		$this->doLogQueryTimes = $b;
	}

	public function stripObjectsAndResources($a, $lv = 0) {
		if (empty($a) || ($lv >= 10)) return $a;
		//echo print_m($a, trim(var_dump_pre($lv, true)));
		$aa = array();
		foreach ($a as $k => $value) {
			$toString = '';
			// echo var_dump_pre($value, 'value');
			if (!is_object($value) && !is_array($value)) {
				$toString = $value.'';
			}
			if (is_object($value)) {
				$value = 'OBJECT ('.get_class($value).')';
			} else if (is_resource($value) || (strpos($toString, 'Resource') !== false)) {
				if (is_resource($value)) {
					$value = 'RESOURCE ('.get_resource_type($value).')';
				} else {
					$value = $toString.' (Unknown)';
				}
			} else if (is_array($value)) {
				$value = $this->stripObjectsAndResources($value, $lv + 1);
			} else if (is_string($value)) {
				if (defined('DIR_FS_DOCUMENT_ROOT')) {
					$value = str_replace(dirname(DIR_FS_DOCUMENT_ROOT), '', $value);
				}
			}
			if ($k == 'args') {
				if (is_string($value) && (strlen($value) > 5000)) {
					$value = substr($value, 0, 5000).'[...]';
				}
			}
			if (($value === $this->access['pass']) && ($this->access['pass'] != null)) {
				$aa = '*****';
				break;
			}
			$aa[$k] = $value;
		}
		return $aa;
	}

	protected function fatalError($query, $errno, $error, $fatal = false) {
		$backtrace = $this->stripObjectsAndResources(debug_backtrace(true));
		$this->sqlErrors[] = array (
			'Query' => rtrim(trim($query, "\n")),
			'Error' => $error,
			'ErrNo' => $errno,
			'Backtrace' => $backtrace
		);
		
		if ($fatal) {
			die(
				'<span style="color:#000000;font-weight:bold;">
					' . $errno . ' - ' . $error . '<br /><br />
					<pre>' . $query . '</pre><br /><br />
					<pre style="font-weight:normal">'.htmlspecialchars(
						print_r($backtrace, true)
					).'</pre><br /><br />
					<small style="color:#ff0000;font-weight:bold;">[SQL Error]</small>
				</span>'
			);
		}
	}

	protected function execQuery($query) {
		$i = 8;
		
		$errno = 0;
		
		$this->selfConnect();
		
		do {
			$errno = 0;
			$result = $this->driver->query($query);
			if ($result === false) {
				$errno = $this->driver->getLastErrorNumber();
			}
			//if (defined('MAGNALISTER_PLUGIN')) echo 'mmysql_query errorno: '.var_export($errno, true)."\n";
			if (($errno === false) || ($errno == 2006)) {
				$this->closeConnection(true);
				usleep(100000);
				$this->selfConnect(true);
			}
			# Retry if '2006 MySQL server has gone away'
		} while (($errno == 2006) && (--$i >= 0));
		
		if ($errno != 0) {
			$this->fatalError($query, $errno, $this->driver->getLastErrorMessage());
		}
	
		return $result;
	}

	/**
	 * Send a query
	 */
	public function query($query, $verbose = false) {
		/* {Hook} "ML_Database_Model_Db_Query": Enables you to extend, modify or log query that goes to the database	.<br>
		   Variables that can be used: <ul><li>$query: The SQL string</li></ul>
		 */
		if (function_exists('magnaContribVerify') && (($hp = magnaContribVerify('ML_Database_Model_Db_Query', 1)) !== false)) {
			require($hp);
		}

		$this->query = $query;
		if ($verbose || false) {
			echo function_exists('print_m') ? print_m($this->query)."\n" : $this->query."\n";
		}
                MLLog::gi()->add('db_query', "### ". $this->count."\n".$this->query."\n");
		$t = microtime(true);
		$this->result = $this->execQuery($this->query);
		$t = microtime(true) - $t;
		$this->querytime += $t;
		if (!$this->result) {
			$this->error = $this->prepareError();
            MLMessage::gi()->addWarn($this->getLastError(), array('query' => $query));
            $aErrorQuery = array (
				'query' => $this->query,
                'error' => $this->getLastError(),
				'time' => $t
			);
			$this->timePerQuery[] = $aErrorQuery;
            MLLog::gi()->add('db_error', $aErrorQuery);
			return false;
		}
		if ($this->doLogQueryTimes) {
			$this->timePerQuery[] = array (
				'query' => $this->query,
                'error' => false,
				'time' => $t
			);
		}
		++$this->count;
		//echo print_m(debug_backtrace());
		
		return $this->result;
	}
	
    public function setCharset($charset) {
        $this->driver->setCharset($charset);
    }
	
//	protected function sessionGarbageCollector() {
//		if ($this->tableExists(TABLE_MAGNA_SESSION)) {
//			$this->query("DELETE FROM ".TABLE_MAGNA_SESSION." WHERE expire < '".(time() - $this->sessionLifetime)."' AND session_id <> '0'");
//		}
//		if (defined('MAGNALISTER_PLUGIN') && MAGNALISTER_PLUGIN && $this->tableExists(TABLE_MAGNA_SELECTION)) {
//			$this->query("DELETE FROM ".TABLE_MAGNA_SELECTION." WHERE expires < '".gmdate('Y-m-d H:i:d', (time() - $this->sessionLifetime))."'");
//		}
//	}

//	protected function sessionRead() {
//		$result = $this->fetchOne('
//			SELECT data FROM '.TABLE_MAGNA_SESSION.'
//			 WHERE session_id = "'.MLShop::gi()->getSessionId().'"
//			       AND expire > "'.time().'"
//		', true);
//		if (!empty($result)) {
//			return @unserialize($result);
//		}
//		return array();
//	}

//	protected function shopSessionRead() {
//		/* This "Session" is for all Backend users and it _never_ expires! */
//		$result = $this->fetchOne('
//			SELECT data FROM '.TABLE_MAGNA_SESSION.'
//			 WHERE session_id = "0"
//		', true);
//
//		if (!empty($result)) {
//			return @unserialize($result);
//		}
//		return array();
//	}
	
//	protected function initSession() {
//		global $_MagnaSession, $_MagnaShopSession;
//		
//		if (defined('TABLE_MAGNA_SESSION') && $this->tableExists(TABLE_MAGNA_SESSION)) {
//			$this->sessionLifetime = $this->getSessionMaxLifeTime();
//			$this->sessionGarbageCollector();
//
//			$_MagnaSession = $this->sessionRead();
//			$_MagnaShopSession = $this->shopSessionRead();
//		}
//	}
	
//        protected function getSessionMaxLifeTime(){
//            $iMaxLifeTime = (int)ini_get("session.gc_maxlifetime");
//            return $iMaxLifeTime > 0 ? $iMaxLifeTime : 300;
//        }
//	protected function sessionStore($data, $sessionID) {
//		if (empty($sessionID) && ($sessionID != '0')) return;
//		
//		$isPluginContext = defined('MAGNALISTER_PLUGIN') && MAGNALISTER_PLUGIN;
//		
//		// only update the session if this class was used from the plugin context
//		// OR if the dirty bit is set. Avoid session updates otherwise.
//		if (!($isPluginContext || (isset($data['__dirty']) && ($data['__dirty'] === true)))) {
//			return;
//		}
//		// remove the dirty bit.
//		if (isset($data['__dirty'])) {
//			unset($data['__dirty']);
//		}
//		if ($this->recordExists(TABLE_MAGNA_SESSION, array('session_id' => $sessionID))) {
//			$this->update(TABLE_MAGNA_SESSION, array(
//				'data' => serialize($data),
//				'expire' => (time() + (($sessionID == '0') ? 0 : $this->sessionLifetime))
//			), array(
//				'session_id' => $sessionID
//			));
//		} else if (!empty($data)) {
//			$this->insert(TABLE_MAGNA_SESSION, array(
//				'session_id' => $sessionID,
//				'data' => serialize($data),
//				'expire' => (time() + (($sessionID == '0') ? 0 : $this->sessionLifetime))
//			), true);
//		}
//	}
	
//	protected function sessionRefresh() {
//		global $_MagnaSession, $_MagnaShopSession;
//		
//		if ($this->tableExists(TABLE_MAGNA_SESSION)) {
//			$this->sessionStore($_MagnaSession, MLShop::gi()->getSessionId());
//			$this->sessionStore($_MagnaShopSession, '0');
//		}
//		
//		// only refresh selection data in magnalister_selection if this class was used from the plugin context
//		if (defined('MAGNALISTER_PLUGIN') && MAGNALISTER_PLUGIN && $this->tableExists(TABLE_MAGNA_SELECTION)) {
//			$this->update(TABLE_MAGNA_SELECTION, array(
//				'expires' => gmdate('Y-m-d H:i:d', (time() + $this->sessionLifetime))
//			), array(
//				'session_id' => MLShop::gi()->getSessionId()
//			));
//		}
//	}
	
	public function escape($object) {
		if (is_array($object)) {
			$object = array_map(array($this, 'escape'), $object);
		} else if (is_string($object)) {
			$tObject = $this->escapeStrings ? stripslashes($object) : $object;
			if ($this->isConnected()) {
				$object =  $this->driver->escape($tObject);
			} else {
				$object = $this->driver->fallbackEscape($tObject);
			}
		}
		return $object;
	}

	/**
	 * Get number of rows
	 */
	public function numRows($result = null) {
		if ($result === null) {
			$result = $this->result;
		}
		
		if ($result === false) {
			return false;
		}
		
		return $this->driver->numRows($result);
	}
	
	/**
	 * Get number of changed/affected rows
	 */
	public function affectedRows() {
		return $this->driver->affectedRows();
	}
	
	/**
	 * Get number of found rows
	 */
	public function foundRows() {
		return $this->fetchOne("SELECT FOUND_ROWS()");
	}
	
	/**
	 * Get a single value
	 */
	public function fetchOne($query) {
		$this->result = $this->query($query);

		if (!$this->result) {
			return false;
		}

		if ($this->numRows($this->result) > 1) {
			$this->error = __METHOD__.' can only return a single value (multiple rows returned).';
			return false;

		} else if ($this->numRows($this->result) < 1) {
			$this->error = __METHOD__.' cannot return a value (zero rows returned).';
			return false;
		}

		$return = $this->fetchNext($this->result);
		if (!is_array($return) || empty($return)) {
			return false;
		}
		$return = array_shift($return);
		if ($return === null) {
			return false;
		}
		return $return;
	}

	/**
	 * Get next row of a result
	 */
	public function fetchNext($result = null) {
		if ($result === null) {
			$result = $this->result;
		}
		
		if ($this->numRows($result) < 1) {
			return false;
		} else {
			$row = $this->driver->fetchArray($result);
			if (!$row) {
				$this->error = $this->prepareError();
				return false;
			}
		}
		
		return $row;
	}

	/**
	 * Fetch a row
	 */
	public function fetchRow($query) {
		$this->result = $this->query($query);

		return $this->fetchNext($this->result);
	}

	public function fetchArray($query, $singleField = false) {
		if ($this->driver->isResult($query)) {
			$this->result = $query;
		} else if (is_string($query)) {
			$this->result = $this->query($query);
		}
		
		if (!$this->result) {
//                            MLMessage::gi()->addWarn($this->getLastError());
			return false;
		}
		
		$array = array();
		
		while ($row = $this->fetchNext($this->result)) {
			if ($singleField && (count($row) == 1)) {
				$array[] = array_pop($row);
			} else {
				$array[] = $row;
			}
		}

		return $array;
	}

	protected function reloadTables() {
		$this->availabeTables = $this->fetchArray('SHOW TABLES', true);
	}

	public function tableExists($table, $purge = false) {
		if ($purge) {
			$this->reloadTables();
		}
		/* {Hook} "ML_Database_Model_Db_TableExists": Enables you to modify the $table variable before the check for existance is performed in
		   case your shop uses a contrib, that messes with the table prefixes.
		 */
		if (function_exists('magnaContribVerify') && (($hp = magnaContribVerify('ML_Database_Model_Db_TableExists', 1)) !== false)) {
			require($hp);
		}
		return in_array($table, $this->availabeTables);
	}

	public function getAvailableTables($pattern = '', $purge = false) {
		if ($purge) {
			$this->reloadTables();
		}
		if (empty($pattern)) {
			return $this->availabeTables;
		}
		$tbls = array();
		foreach ($this->availabeTables as $t) {
			if (preg_match($pattern, $t)) {
				$tbls[] = $t;
			}
		}
		return $tbls;
	}

	public function tableEmpty($table) {
		return ($this->fetchOne('SELECT * FROM '.$table.' LIMIT 1') === false);
	}

	public function mysqlVariableValue($variable) {
		$showVariablesLikeVariable = $this->fetchRow("SHOW VARIABLES LIKE '$variable'");
		if ($showVariablesLikeVariable) {
			return $showVariablesLikeVariable['Value'];
		}
		# nicht false zurueckgeben, denn dies koennte ein gueltiger Variablenwert sein
		return null;
	}
	
	public function mysqlSetHigherTimeout($timeoutToSet = 3600) {
		if ($this->mysqlVariableValue('wait_timeout') < $timeoutToSet) {
			$this->query("SET wait_timeout = $timeoutToSet");
		}
		if ($this->mysqlVariableValue('interactive_timeout') < $timeoutToSet) {
			$this->query("SET interactive_timeout = $timeoutToSet");
		}
	}

	public function tableEncoding($table) {
		$showCreateTable = $this->fetchRow('SHOW CREATE TABLE `'.$table.'`');
		if (preg_match("/CHARSET=([^\s]*).*/", $showCreateTable['Create Table'], $matched)) {
			return $matched[1];
		}
		$charSet = $this->mysqlVariableValue('character_set_database');
		if (empty($charSet)) return false;
		return $charSet;
	}


	public function	columnExistsInTable($column, $table) {
		if (isset($this->columnExistsInTableCache[$table][$column])) {
			return $this->columnExistsInTableCache[$table][$column];
		}
		$columns = $this->fetchArray('DESC  '.$table);
		if (!is_array($columns) || empty($columns)) {
			return false;
		}
		foreach ($columns as $column_description) {
			if ($column_description['Field'] == $column) {
				$this->columnExistsInTableCache[$table][$column] = true;
				return true;
			}
		}
		$this->columnExistsInTableCache[$table][$column] = false;
		return false;
	}

	public function	columnType($column, $table) {
		$columns = $this->fetchArray('DESC  '.$table);
		foreach($columns as $column_description) {
			if($column_description['Field'] == $column) return $column_description['Type'];
		}
		return false;
	}

	public function recordExists($table, $conditions, $getQuery = false) {
		if (!is_array($conditions) || empty($conditions)) {
			trigger_error(sprintf("%s: Second parameter has to be an array may not be empty!", __FUNCTION__), E_USER_WARNING);
		}
		$fields = array();
		$values = array();
		foreach ($conditions as $f => $v) {
			$values[] = '`'.$f."` = '".$this->escape($v)."'";
		}
		if ($getQuery) {
			$q = 'SELECT * FROM `'.$table.'` WHERE '.implode(' AND ', $values);
			return $q;	
		}else{
			$q = 'SELECT 1 FROM `'.$table.'` WHERE '.implode(' AND ', $values).' LIMIT 1';
		}
		$result = $this->fetchOne($q);
		if ($result !== false) {
			return true;
		}
		return false;
	}
	
	/**
	 * Insert an array of values
	 */
	public function insert($tableName, $data, $replace = false) {
		if (!is_array($data)) {
			$this->error = __METHOD__.' expects an array as 2nd argument.';
			return false;
		}

		$cols = '(';
		$values = '(';
		foreach ($data as $key => $value) {
			$cols .= "`" . $key . "`, ";

			if ($value === null) {
				$values .= 'NULL, ';
			} else if (is_int($value) || is_float($value) || is_double($value)) {
				$values .= $value . ", ";
			} else if (strtoupper($value) == 'NOW()') {
				$values .= "NOW(), ";
			} else {
				$values .= "'" . $this->escape($value) . "', ";
			}
		}
		$cols = rtrim($cols, ", ") . ")";
		$values = rtrim($values, ", ") . ")";
		#if (function_exists('print_m')) echo print_m(($replace ? 'REPLACE' : 'INSERT').' INTO `'.$tableName.'` '.$cols.' VALUES '.$values);
		return $this->query(($replace ? 'REPLACE' : 'INSERT').' INTO `'.$tableName.'` '.$cols.' VALUES '.$values);
	}

	/**
	 * Insert an array of values
	 */
	public function batchinsert($tableName, $data, $replace = false) {
		if (!is_array($data)) {
			$this->error = __METHOD__.' expects an array as 2nd argument.';
			return false;
		}
		$state = true;

		$cols = '(';
		foreach ($data[0] as $key => $val) {
			$cols .= "`" . $key . "`, ";
		}
		$cols = rtrim($cols, ", ") . ")";

		$block = array_chunk($data, 20);
		
		foreach ($block as $data) {
			$values = '';
			foreach ($data as $subset) {
				$values .= ' (';
				foreach ($subset as $value) {
					if ($value === null) {
						$values .= 'NULL, ';
					} else if (is_int($value) || is_float($value) || is_double($value)) {
						$values .= $value . ", ";
					} else if (strtoupper($value) == 'NOW()') {
						$values .= "NOW(), ";
					} else {
						$values .= "'" . $this->escape($value) . "', ";
					}
				}
				$values = rtrim($values, ", ") . "),\n";
			}
			$values = rtrim($values, ",\n");
	
			//echo ($replace ? 'REPLACE' : 'INSERT').' INTO `'.$tableName.'` '.$cols.' VALUES '.$values;
			$state = $state && $this->query(($replace ? 'REPLACE' : 'INSERT').' INTO `'.$tableName.'` '.$cols.' VALUES '.$values);
		}
		return $state;
	}

	/**
	 * Get last auto-increment value
	 */
	public function getLastInsertID() {
		return $this->driver->getInsertId();
	}

	/**
	 * Update row(s)
	 */
	public function update($tableName, $data, $wherea = array(), $add = '', $verbose = false) {
		if (!is_array($data) || !is_array($wherea)) {
			$this->error = __METHOD__.' expects two arrays as 2nd and 3rd arguments.';
			return false;
		}

		$values = "";
		$where = "";

		foreach ($data as $key => $value) {
			$values .= "`" . $key . "` = ";

			if ($value === null) {
				$values .= 'NULL, ';
			} else if (is_int($value) || is_float($value) || is_double($value)) {
				$values .= $value . ", ";
			} else if (strtoupper($value) == 'NOW()') {
				$values .= "NOW(), ";
			} else {
				$values .= "'" . $this->escape($value) . "', ";
			}
		}
		$values = rtrim($values, ", ");

		if (!empty($wherea)) {
			foreach ($wherea as $key => $value) {
				$where .= "`" . $key . "` ";
	
				if ($value === null) {
					$where .= 'IS NULL AND ';
				} else if (is_int($value) || is_float($value) || is_double($value)) {
					$where .= '= '.$value . " AND ";
				} else if (strtoupper($value) == 'NOW()') {
					$where .= "= NOW() AND ";
				} else {
					$where .= "= '" . $this->escape($value) . "' AND ";
				}
			}
			$where = rtrim($where, "AND ");
		} else {
			$where = '1=1';
		}
		return $this->query('UPDATE `'.$tableName.'` SET '.$values.' WHERE '.$where.' '.$add, $verbose);
	}

	/**
	 * Delete row(s)
	 */
	public function delete($table, $wherea, $add = null) {
		if (!is_array($wherea)) {
			$this->error = __METHOD__.' expects an array as 2nd argument.';
			return false;
		}

		$where = "";

		foreach ($wherea as $key => $value) {
			$where .= "`" . $key . "` ";

			if ($value === null) {
				$where .= 'IS NULL AND ';
			} else if (is_int($value) || is_float($value) || is_double($value)) {
				$where .= '= '.$value . " AND ";
			} else {
				$where .= "= '" . $this->escape($value) . "' AND ";
			}
		}

		$where = rtrim($where, "AND ");

		$query = "DELETE FROM `".$table."` WHERE ".$where." ".$add;

		return $this->query($query);
	}

	public function freeResult($result = null) {
		if ($result === null) {
			$result = $this->result;
		}
		$this->driver->freeResult($result);
		return true;
	}

	/**
	 * Unescapes strings / arrays of strings
	 */
	public function unescape($object) {
		return is_array($object)
			? array_map(array($this, 'unescape'), $object)
			: stripslashes($object);
	}
	
	public function getTableCols($table) {
		$cols = array();
		if (!$this->tableExists($table)) {
			return $cols;
		}
		$colsQuery = $this->query('SHOW COLUMNS FROM `'.$table.'`');
		while ($row = $this->fetchNext($colsQuery))	{
			$cols[] = $row['Field'];
		}
		$this->freeResult($colsQuery);
		return $cols;
	}

	/**
	 * Get last executed query
	 */
	public function getLastQuery() {
		return $this->query;
	}

	/**
	 * Get last error
	 */
	public function getLastError() {
		return $this->error;
	}
	
	/**
	 * Gets all SQL errors.
	 */
	public function getSqlErrors() {
		return $this->sqlErrors;
	}
	
	/**
	 * Get time consumed for all queries / operations (milliseconds)
	 */
	public function getQueryTime() {
		return round((microtime(true) - $this->start) * 1000, 2);
	}

	public function getTimePerQuery() {
		return $this->timePerQuery;
	}

	/**
	 * Get number of queries executed
	 */
	public function getQueryCount() {
		return $this->count;
	}
	
	public function getRealQueryTime() {
		return $this->querytime;
	}
	
	public function setShowDebugOutput($b) {
		$this->showDebugOutput = $b;
	}

}
