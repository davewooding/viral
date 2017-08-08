<?php
$mysqli_db = "viral";                    // database name
$mysqli_username = "root";             // database username
$mysqli_password = "";                      // database password
$mysqli_host = "localhost";                        // database host



/*
 * function clean_up_auto_increment
 *
 */
function clean_up_auto_increment($mysqli_table, $column) {
    global $db;
    $sql = "ALTER TABLE $mysqli_table DROP COLUMN $column";
    $query = mysqli_query($sql);
    $error = mysqli_error();
    if ($debug == "yes") print $error."<br>";
    $sql = "ALTER TABLE $mysqli_table ADD counter INT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY ($column)";
    $query = mysqli_query($sql);
    $error = mysqli_error();
    //if ($error) die($error);
    if ($debug == "yes") print $error."<br>";
}



/*
 * function connectDatabase
 *
 */
function connectDatabase($mysqli_host, $mysqli_username, $mysqli_password, $mysqli_db) {
    // clear up variables
    $mysqli_db = trim($mysqli_db);
    $mysqli_username = trim($mysqli_username);
    $mysqli_password = trim($mysqli_password);
    $mysqli_host = trim($mysqli_host);
    // connect
    $db = mysqli_connect($mysqli_host, $mysqli_username, $mysqli_password, $mysqli_db);
    $error = mysqli_error($db);
    if (preg_match("/Access denied for/", $error)) $error = "Incorrect database username and/or database password in ".$filename;
    if (preg_match("/Unknown MySQL Server Host/", $error)) $error = "Incorrect database host in ".$filename;
    if ($error) die($error);
    mysqli_select_db($db,$mysqli_db);
    return $db;
}



/*
 * function closeDatabase
 *
 */
function closeDatabase($db) {
    // close connection to database
    mysqli_close($db);
}



/*
 * function quote_smart
 *
 */
function quote_smart($value) {
    global $db;
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if ($value == '') {
        return 'NULL';
    } elseif (!is_numeric($value) || $value[0] == '0') {
        return "'" . mysqli_real_escape_string($db,$value) . "'";
    }
}



/*
 * function pagination
 *
 */
function pagination($table, $extra=NULL, $pageno, $rows_per_page) {
    global $db;
    $sql = "SELECT * FROM $table $extra";
    $query = mysqli_query($db,$sql);
    $mysql_error = mysqli_error($db);
    $numrows = mysqli_num_rows($query);
    // last page
    $lastpage = ceil($numrows/$rows_per_page);
    // check page number is within limits
    $pageno = (int)$pageno;
    if ($pageno > $lastpage) {
        $pageno = $lastpage;
    }
    if ($pageno < 1) {
        $pageno = 1;
    }
    // limit
    $limit = "";
    $limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
    // display
    $request_uri = preg_replace("/q=search&term=(.*)&/", "&", $_SERVER['REQUEST_URI']);
    $request_uri = preg_replace("/\?pageno=\d+/", "", $_SERVER['REQUEST_URI']);
    $pagination_display = "";
    if ($pageno == 1) {
        $pagination_display .= " FIRST PREV ";
    } else {
        $pagination_display .= " <a href=\"".$request_uri."?pageno=1\">FIRST</a> ";
        $prevpage = $pageno-1;
        $pagination_display .= " <a href=\"".$request_uri."?pageno=".$prevpage."\">PREV</a> ";
    }
    $pagination_display .= " ( Page ".$pageno." of ".$lastpage." ) ";
    if ($pageno == $lastpage) {
        $pagination_display .= " NEXT LAST ";
    } else {
        $nextpage = $pageno+1;
        $pagination_display .= " <a href=\"".$request_uri."?pageno=".$nextpage."\">NEXT</a> ";
        $pagination_display .= " <a href=\"".$request_uri."?pageno=".$lastpage."\">LAST</a> ";
    }
    if ($rows_per_page >= $numrows) $pagination_display = " ( Page ".$pageno." of ".$lastpage." ) ";
    $pagination_display_array[0] = $pagination_display;
    $pagination_display_array[1] = $limit;
    return $pagination_display_array;
}


?>
<?php
/*
CREATE TABLE IF NOT EXISTS `results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `email_logger` varchar(255) NOT NULL,
  `action_logger` varchar(255) NOT NULL,
  `result` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
*/
?>
