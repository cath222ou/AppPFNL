<?php

  #$_POST["email"] = "olivier.dupras-tessier@usherbrooke.ca"; 
  #$_POST["id"] = "14"; 

  class MyDB extends SQLite3 {
    function __construct() {
      $this->open('mysqlitedb.db');
    }
  }


  /*
   * Valid if the user parameters are realy exist in our database
   * @params { SQLITE3_DB, STRING }
   * @return { array() }
   */
  function validUser( $db, $email ) {
    $statement = "SELECT count(id_user) as exist, id_user as id"; 
    $table =  "FROM user "; 
    $condition = "WHERE code_email LIKE '".$_POST["email"]."'"; 
    $request = join( " ", array( $statement, $table, $condition ) ); 

    $row = $db->query($request); 
    $results = $row->fetchArray(SQLITE3_ASSOC);
    
    return $results; 
  }; /** validUser() */ 


  /*
   * Valid if the ntfp and the species from iqh "ID"
   * @params { SQLITE3_DB, STRING }
   * @return { array() }
   */
  function validIqh( $db, $id ) { 
    $statement = "SELECT pfnl.name as pfnl, espece.name as espece"; 
    $table =  "FROM pfnl, espece "; 
    $condition = "WHERE espece.id_espece = ( SELECT id_espece FROM iqh WHERE id_iqh = '". $id ."' ) AND pfnl.id_pfnl = ( SELECT id_pfnl FROM espece WHERE id_espece = ( SELECT id_espece FROM iqh WHERE id_iqh = '". $id ."') );"; 
    $request = join( " ", array( $statement, $table, $condition ) ); 

    $row = $db -> query($request); 
    $results = $row -> fetchArray(SQLITE3_ASSOC);

    #print_r( $results ); 
    
    return $results; 
  } /** validIqh() */ 


  /*
   * Valid if the ntfp and the species from iqh "ID"
   * @params { SQLITE3_DB, STRING }
   * @return { array() }
   */
  function validProject( $db, $id ) { 
    $statement = "SELECT name, type"; 
    $table =  "FROM project "; 
    $condition = "WHERE id_project LIKE '". $id ."';"; 
    $request = join( " ", array( $statement, $table, $condition ) ); 

    $row = $db -> query($request); 
    $results = $row -> fetchArray(SQLITE3_ASSOC);

    #print_r( $results ); 
    
    return $results; 
  } /** validIqh() */ 


  /*
   * Get every user notice
   * @params { SQLITE3_DB, STRING }
   * @return { array() }
   */
  function getNotice( $db, $user ) {
    $statement = "SELECT id_notice as id, type, status, date, text"; 
    $table = "FROM notice"; 
    $condition = "WHERE id_notice IN ( SELECT id_notice FROM user_notice WHERE id_user LIKE 1 )"; 
    $request = join( " ", array( $statement, $table, $condition ) ); 
    $row = $db->query($request); 

    $results = array(); 
    $count = 0; 
    while ( $res = $row -> fetchArray( SQLITE3_ASSOC ) ) { 
      $results[ $count ] = $res; 
      $count++; 
    }

    return $results;  
  } /** getNotice() */ 


  /*
   * Create a new notice's message based on notice's type
   * @params { array() }
   * @return { STRING }
   */
  function setMessage( $db, $obj ) {
    if ( $obj["type"] == "iqh" ) {
      $res = validIqh( $db, $obj["text"] );  
      $header = join( " ", array( 'L\'IQH n°', $obj["text"] ) ); 
      $text = join( " - ", $res ); 
      $icon = "fa fa-tree text-green"; 
    } else if ( $obj["type"] == "project" ) {
      $res = validProject( $db, $obj["text"] ); 
      if ( $res["type"] == "personal" ) { 
        $header = 'Un nouveau projet'; 
        $text = join( " - ", array( "Projet personnel", $res["name"] ) ); 
        $icon = "fa fa-file-text text-blue"; 
      } else if ( $res["type"] == "group" ) { 
        $header = 'Un nouveau projet'; 
        $text = join( " - ", array( "Projet groupe&#769", $res["name"] ) ); 
        $icon = "fa fa-file-text text-green"; 
      } else if ( $res["type"] == "public" ) { 
        $header = 'Un nouveau projet'; 
        $text = join( " - ", array( "Projet publique", $res["name"] ) ); 
        $icon = "fa fa-file-text text-yellow"; 
      } 
    } 
    $header =  ( $obj["status"] == "created" ) ? join( " ", array( $header, 'est disponible' ) ) : 
      null; 

    $results = '<h4 style="margin: 0; !important">'. $header .'<p class="pull-right"><i class="'. $icon .'"></i></p></h4><p style="margin: 0; !important">'. $text .'</p>'; 

    return $results; 
  } /** setMessage() */ 


  /*
   * Create a new notice for each notice from SQLiteDB
   * @params { array() }
   * @return { STRING }
   */
  function listNotice( $db, $obj ) {
    $results = array(); 
    $results["list"] = '<ul class="menu">'; 
    $results["count"] = 0; 

    foreach( $obj as $notice ) {
      $message = setMessage( $db, $notice ); 
      $results["list"] = $results["list"].'<li id="'. $notice["id"] .'"><a href="#">'. $message .'</a></li>'; 
      $results["count"]++; 
    } 
    $results["list"] = $results["list"].'</ul>'; 

    return $results; 
  } /** listNotice() */ 


  /*
   * Define the header's text
   * @params { INT }
   * @return { STRING }
   */
  function setHeader( $obj ) {
    $results = ( ( $obj == 1 ) ? $obj." nouvel avis disponible " : $obj." nouvels avis disponibles " ); 

    return $results; 
  } /** setHeader() */


  /*
   * Create the new notification element
   * @params { array() }
   * @return { array() }
   */
  function setNotice( $db, $obj ) {
    $results = array(); 
    $res = listNotice( $db, $obj ); 
    if ( $res["count"] > 0 ) {
      $header = setHeader( $res["count"] ); 
      $results["parent"] = "ul.navbar-nav"; 
      $results["node"] = "li.messages-menu#notification"; 
      $results["last"] = "li.messages-menu#notification"; 
      $results["action"] = "replaceWith"; 
      $results["html"] = '<li class="dropdown messages-menu" id="notification">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-bell-o"></i>
          <span class="label label-warning">'. $res["count"] .' </span>
        </a>
        <ul class="dropdown-menu">
          <li class="header">'. $header .'</li>
          <li>'. $res["list"] .'</li>
          <li class="footer"><a href="#">Consultez touts les avis </a></li>
        </ul>
      </li>'; 
    } 

    return $results; 
  } /** setNotice() */ 


  /*
   * Delete SQLiteDB ROW where id_user AND id_notice match with $_POST[] argument
   * @params { SQLiteDB, STRING, STRING }
   * @return { --- }
   */
  function checkNotice( $db, $user, $notice ) {
    $statement = "DELETE"; 
    $table =  "FROM user_notice"; 
    $condition = "WHERE id_user LIKE '". $user ."' AND id_notice LIKE '". $notice ."';"; 
    $request = join( " ", array( $statement, $table, $condition ) ); 

    $results = $db -> query( $request ); 
  } /**  checkNotice() */


  $db = new MyDB();
  $results = array(); 
  $res = array(); 

  $res["user"] = validUser( $db, $_POST["email"] ); 
  if ( $res["user"]["exist"] > 0 ) { 
    $results["exist"] = $res["user"]["exist"]; 
    $results["panel"] = array(); 
    $i = 0; 

    if ( isset( $_POST["id"] ) ) { 
      checkNotice( $db, $res["user"]["id"], $_POST["id"] );  
    } else {
      $res["notice"] = getNotice( $db, $res["user"]["id"] ); 
      $results["panel"][$i] = setNotice( $db, $res["notice"] ); 
    }  

  } else {
    $results["exist"] = 0; 
  }

  echo json_encode( $results ); 

?>