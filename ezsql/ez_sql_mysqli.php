<?php

	/**********************************************************************
	*  Author: Justin Vincent (jv@jvmultimedia.com)
	*  Web...: http://twitter.com/justinvincent
	*  Name..: ezSQL_mysqli
	*  Desc..: mySQLi component (part of ezSQL databse abstraction library)
	*
	*  ez_sql_mysql.php'nin PHP 8.3 için mysqli karşılığı. Public API
	*  (get_var, get_row, get_results, query, escape, insert_id ...) aynıdır.
	*/

	/**********************************************************************
	*  ezSQL error strings - mySQLi
	*/

	global $ezsql_mysqli_str;

	$ezsql_mysqli_str = array
	(
		1 => 'Require $dbuser and $dbpassword to connect to a database server',
		2 => 'Error establishing mySQLi database connection. Correct user/password? Correct hostname? Database server running?',
		3 => 'Require $dbname to select a database',
		4 => 'mySQLi database connection is not active',
		5 => 'Unexpected error while trying to select database'
	);

	/**********************************************************************
	*  ezSQL Database specific class - mySQLi
	*/

	if ( ! function_exists ('mysqli_connect') ) die('<b>Fatal Error:</b> ezSQL_mysqli requires mySQLi Lib to be compiled and or linked in to the PHP engine');
	if ( ! class_exists ('ezSQLcore') ) die('<b>Fatal Error:</b> ezSQL_mysqli requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');

	class ezSQL_mysqli extends ezSQLcore
	{

		var $dbuser = false;
		var $dbpassword = false;
		var $dbname = false;
		var $dbhost = false;
		var $encoding = false;
		var $rows_affected = false;
		var $dbh = null;
		var $result = null;

		/**********************************************************************
		*  Constructor - allow the user to perform a qucik connect at the
		*  same time as initialising the ezSQL_mysqli class
		*/

		function __construct($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost', $encoding='')
		{
			$this->dbuser = $dbuser;
			$this->dbpassword = $dbpassword;
			$this->dbname = $dbname;
			$this->dbhost = $dbhost;
			$this->encoding = $encoding;
		}

		/**********************************************************************
		*  Short hand way to connect to mySQLi database server
		*  and select a mySQLi database at the same time
		*/

		function quick_connect($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost', $encoding='')
		{
			$return_val = false;
			if ( ! $this->connect($dbuser, $dbpassword, $dbhost) ) ;
			else if ( ! $this->select($dbname,$encoding) ) ;
			else $return_val = true;
			return $return_val;
		}

		/**********************************************************************
		*  Try to connect to mySQLi database server
		*/

		function connect($dbuser='', $dbpassword='', $dbhost='localhost')
		{
			global $ezsql_mysqli_str; $return_val = false;

			// Keep track of how long the DB takes to connect
			$this->timer_start('db_connect_time');

			// PHP 8.1+ varsayılanı exception fırlatmak; ezSQL hata akışı false dönüşüne dayanıyor
			mysqli_report(MYSQLI_REPORT_OFF);

			list( $host, $port ) = $this->get_host_port( $dbhost, 3306 );

			// Must have a user and a password
			if ( ! $dbuser )
			{
				$this->register_error($ezsql_mysqli_str[1].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysqli_str[1],E_USER_WARNING) : null;
			}
			// Try to establish the server database handle
			else if ( ! $this->dbh = @mysqli_connect($host,$dbuser,$dbpassword,'',$port) )
			{
				$this->register_error($ezsql_mysqli_str[2].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysqli_str[2],E_USER_WARNING) : null;
			}
			else
			{
				$this->dbuser = $dbuser;
				$this->dbpassword = $dbpassword;
				$this->dbhost = $dbhost;
				$return_val = true;
			}

			return $return_val;
		}

		/**********************************************************************
		*  Try to select a mySQLi database
		*/

		function select($dbname='', $encoding='')
		{
			global $ezsql_mysqli_str; $return_val = false;

			// Must have a database name
			if ( ! $dbname )
			{
				$this->register_error($ezsql_mysqli_str[3].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysqli_str[3],E_USER_WARNING) : null;
			}

			// Must have an active database connection
			else if ( ! $this->dbh )
			{
				$this->register_error($ezsql_mysqli_str[4].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysqli_str[4],E_USER_WARNING) : null;
			}

			// Try to connect to the database
			else if ( !@mysqli_select_db($this->dbh,$dbname) )
			{
				// Try to get error supplied by mysqli if not use our own
				if ( !$str = @mysqli_error($this->dbh))
					  $str = $ezsql_mysqli_str[5];

				$this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
			}
			else
			{
				$this->dbname = $dbname;
				if ( $encoding == '') $encoding = $this->encoding;
				if ( $encoding != '' )
				{
					// set_charset hem sunucu bağlantısını hem de mysqli_real_escape_string'i etkiler
					mysqli_set_charset($this->dbh, strtolower(str_replace("-","",$encoding)));
				}

				$return_val = true;
			}

			return $return_val;
		}

		/**********************************************************************
		*  Format a mySQLi string correctly for safe mySQLi insert
		*/

		function escape($str)
		{
			// If there is no existing database connection then try to connect
			if ( ! isset($this->dbh) || ! $this->dbh )
			{
				$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
				$this->select($this->dbname, $this->encoding);
			}

			if ( ! $this->dbh )
				return addslashes((string) $str);

			return mysqli_real_escape_string($this->dbh, stripslashes((string) $str));
		}

		/**********************************************************************
		*  Return mySQLi specific system date syntax
		*  i.e. Oracle: SYSDATE Mysql: NOW()
		*/

		function sysdate()
		{
			return 'NOW()';
		}

		/**********************************************************************
		*  Perform mySQLi query and try to detirmin result value
		*/

		function query($query)
		{

			// This keeps the connection alive for very long running scripts
			if ( $this->num_queries >= 500 )
			{
				$this->num_queries = 0;
				$this->disconnect();
				$this->quick_connect($this->dbuser,$this->dbpassword,$this->dbname,$this->dbhost,$this->encoding);
			}

			// Initialise return
			$return_val = 0;

			// Flush cached values..
			$this->flush();

			// For reg expressions
			$query = trim($query);

			// Log how the function was called
			$this->func_call = "\$db->query(\"$query\")";

			// Keep track of the last query for debug..
			$this->last_query = $query;

			// Count how many queries there have been
			$this->num_queries++;

			// Start timer
			$this->timer_start($this->num_queries);

			// Use core file cache function
			if ( $cache = $this->get_cache($query) )
			{
				// Keep tack of how long all queries have taken
				$this->timer_update_global($this->num_queries);

				// Trace all queries
				if ( $this->use_trace_log )
				{
					$this->trace_log[] = $this->debug(false);
				}

				return $cache;
			}

			// If there is no existing database connection then try to connect
			if ( ! isset($this->dbh) || ! $this->dbh )
			{
				$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
				$this->select($this->dbname,$this->encoding);
				// No existing connection at this point means the server is unreachable
				if ( ! isset($this->dbh) || ! $this->dbh )
					return false;
			}

			// Perform the query via std mysqli_query function..
			$this->result = @mysqli_query($this->dbh,$query);

			// If there is an error then take note of it..
			if ( $str = @mysqli_error($this->dbh) )
			{
				$this->register_error($str);
				$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
				return false;
			}

			// Query was an insert, delete, update, replace
			if ( preg_match("/^(insert|delete|update|replace|truncate|drop|create|alter|set)\s+/i",$query) )
			{
				$is_insert = true;
				$this->rows_affected = @mysqli_affected_rows($this->dbh);

				// Take note of the insert_id
				if ( preg_match("/^(insert|replace)\s+/i",$query) )
				{
					$this->insert_id = @mysqli_insert_id($this->dbh);
				}

				// Return number fo rows affected
				$return_val = $this->rows_affected;
			}
			// Sonuç kümesi döndürmeyen diğer sorgular (START TRANSACTION vb.)
			else if ( ! ($this->result instanceof mysqli_result) )
			{
				$is_insert = true;
				$return_val = 0;
			}
			// Query was a select
			else
			{
				$is_insert = false;

				// Take note of column info
				$i=0;
				while ($i < @mysqli_num_fields($this->result))
				{
					$this->col_info[$i] = @mysqli_fetch_field($this->result);
					$i++;
				}

				// Store Query Results
				$num_rows=0;
				while ( $row = @mysqli_fetch_object($this->result) )
				{
					// Store relults as an objects within main array
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}

				@mysqli_free_result($this->result);

				// Log number of rows the query returned
				$this->num_rows = $num_rows;

				// Return number of rows selected
				$return_val = $this->num_rows;
			}

			// disk caching of queries
			$this->store_cache($query,$is_insert);

			// If debug ALL queries
			$this->trace || $this->debug_all ? $this->debug() : null ;

			// Keep tack of how long all queries have taken
			$this->timer_update_global($this->num_queries);

			// Trace all queries
			if ( $this->use_trace_log )
			{
				$this->trace_log[] = $this->debug(false);
			}

			return $return_val;

		}

		/**********************************************************************
		*  Close the active mySQLi connection
		*/

		function disconnect()
		{
			if ( $this->dbh )
			{
				@mysqli_close($this->dbh);
			}
			$this->dbh = null;
		}

	}
