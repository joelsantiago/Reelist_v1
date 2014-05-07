<?php
// MySQL class that takes database information and creates connection
class MySQL {
    var $host;
    var $dbUser;
    var $dbPass;
    var $dbName;
    var $dbConn;
    var $connectError;

	// Constructor assigns passed database information to local variables for use within local functions
    function MySQL ($host,$dbUser,$dbPass,$dbName) {
        $this->host=$host;
        $this->dbUser=$dbUser;
        $this->dbPass=$dbPass;
        $this->dbName=$dbName;
        $this->connectToDb();
    }
	
	// Function that connects to database and displays error if connection fails
    function connectToDb () {
        if (!$this->dbConn = @mysql_connect($this->host,
                                      $this->dbUser,
                                      $this->dbPass)) {
            trigger_error('Could not connect to server');
            $this->connectError=true;
        } else if ( !@mysql_select_db($this->dbName,$this->dbConn) ) {
            trigger_error('Could not select database');
            $this->connectError=true;
        }
    }

	// Error handling function that displays error based on several checks
    function isError () {
        if ( $this->connectError )
            return true;
        $error=mysql_error ($this->dbConn);
        if ( empty ($error) )
            return false;
        else
            return true;
    }

	// Query function that connects to database and queries with passed query string, displays error if the requested query fails
    function query($sql) {
        if (!$queryResource=mysql_query($sql,$this->dbConn))
            trigger_error ('Query failed: '.mysql_error($this->dbConn).
                           ' SQL: '.$sql);
        return new MySQLResult($this,$queryResource);
    }
}

// MySQLResult class that takes the results from the passed query and determines what the outcome should be
class MySQLResult {
    var $mysql;
    var $query;
	
	// Constructor assigns the database informatino and query string to local variables for function use
    function MySQLResult(& $mysql,$query) {
        $this->mysql=& $mysql;
        $this->query=$query;
    }

	// fetch() determines if the returned values within the array $row are greater than 0 and determines the specified outcome
    function fetch () {
        if ( $row=mysql_fetch_array($this->query,MYSQL_ASSOC) ) {
            return $row;
        } else if ( $this->size() > 0 ) {
            mysql_data_seek($this->query,0);
            return false;
        } else {
            return false;
        }
    }

	// size() returns the size of the database being queried
    function size () {
        return mysql_num_rows($this->query);
    }

	// insertID() inserts a specified ID into the database being queried
    function insertID () {
        return mysql_insert_id($this->mysql->dbConn);
    }
    
	// isError() returns an error if the query fails
    function isError () {
        return $this->mysql->isError();
    }
}
?>