<?php

/***
 * mysql database utils
 */
class DB
{
    var $link = null;

    function __construct($db_host, $db_user, $db_pass, $db_name, $db_port)
    {

        $this->link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

        if (!$this->link) die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        mysqli_query($this->link, "set sql_mode = ''");
        mysqli_query($this->link, "set character set 'utf8'");
        mysqli_query($this->link, "set names 'utf8'");

        return true;
    }

    function fetch($q)
    {
        return mysqli_fetch_assoc($q);
    }

    function get_row($q)
    {
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            die('Query Error: ' . mysqli_error($this->link));
        }
        return mysqli_fetch_assoc($result);
    }

    function get_rows($q)
    {
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            die('Query Error: ' . mysqli_error($this->link));
        }
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    function count($q)
    {
        $result = mysqli_query($this->link, $q);
        $count = mysqli_fetch_array($result);
        return $count[0];
    }

    function query($q)
    {
        return mysqli_query($this->link, $q);
    }

    function escape($str)
    {
        return mysqli_real_escape_string($this->link, $str);
    }

    function insert($q)
    {
        if (mysqli_query($this->link, $q))
            return mysqli_insert_id($this->link);
        return false;
    }

    function affected()
    {
        return mysqli_affected_rows($this->link);
    }

    function insert_array($table, $array)
    {
        $q = "INSERT INTO `$table`";
        $q .= " (`" . implode("`,`", array_keys($array)) . "`) ";
        $q .= " VALUES ('" . implode("','", array_values($array)) . "') ";

        if (mysqli_query($this->link, $q))
            return mysqli_insert_id($this->link);
        return false;
    }

    function error()
    {
        $error = mysqli_error($this->link);
        $errno = mysqli_errno($this->link);
        return '[' . $errno . '] ' . $error;
    }

    function close()
    {
        $q = mysqli_close($this->link);
        return $q;
    }

    function update($q)
    {
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            die('Query Error: ' . mysqli_error($this->link));
        }
        return mysqli_affected_rows($this->link) > 0;
    }

    function get_var($query)
    {
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            die('Query Error: ' . mysqli_error($this->link));
        }
        $row = mysqli_fetch_array($result);
        return $row[0];
    }

    function get_results($query, $params = [])
    {
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->link, $query);
            if ($stmt === false) {
                die('Prepare Error: ' . mysqli_error($this->link));
            }

            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $result = mysqli_query($this->link, $query);
        }

        if (!$result) {
            die('Query Error: ' . mysqli_error($this->link));
        }

        $rows = [];
        while ($row = mysqli_fetch_object($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

}