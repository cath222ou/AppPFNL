<?php

	#$_POST["email"] = "olivier.dupras-tessier@usherbrooke.ca"; 
	#$_POST["type"] = "mushroom"; 


	class MyDB extends SQLite3 {
    function __construct() {
      $this->open('mysqlitedb.db');
    }
	}


	/**
	 * Get the real employed function name of a status 
	 * @params { $_POST['status'] }
	 */
	function getStatusRole( $obj ) {
		if ( $obj == "membre" ) {
			$results = "Membre";
		} else if ( $obj == "developpeur" ) {
			$results = "De&#769veloppeur";
		} else if ( $obj == "admin" ) {
			$results = "Administrateur";
		}

		return $results; 
	} /** getStatusRole() */


	/** 
	 * Turn date to French date abreviation
	 * @params { STRING }
	 */ 
	function getFrenchDate( $obj ) {
		list( $day, $month, $year ) = split( '[/.-]', $obj );
		if ( $month == "01" ) {
			$results = "Janv. ".$year; 
		} else if ( $month == "02" ) {
			$results = "Fe&#769vr. ".$year; 
		} else if ( $month == "03" ) {
			$results = "Mars ".$year; 
		} else if ( $month == "04" ) {
			$results = "Avr. ".$year; 
		} else if ( $month == "05" ) {
			$results = "Mai ".$year; 
		} else if ( $month == "06" ) {
			$results = "Juin ".$year; 
		} else if ( $month == "07" ) {
			$results = "Juill. ".$year; 
		} else if ( $month == "08" ) {
			$results = "Aou&#770t ".$year; 
		} else if ( $month == "09" ) {
			$results = "Sept. ".$year; 
		} else if ( $month == "10" ) {
			$results = "Oct. ".$year; 
		} else if ( $month == "11" ) {
			$results = "Nov. ".$year; 
		} else if ( $month == "12" ) {
			$results = "De&#769c. ".$year; 
		}

		return $results; 
	} /** getFrenchDate() */


	/** 
	 * Define profil picture from user gender
	 * @params { STRING }
	 */ 
	function getProfileImg( $obj ) {	
		if ($obj == "male") {
			$results = "avatar5.png"; 
		} else if ($obj == "female") {
			$results = "avatar2.png"; 
		}

		return $results; 
	}; /** getProfileImg() */


	/*
	 * Valid if the user parameters are realy exist in our database
	 * @params { SQLITE3_DB, STRING }
	 * @return { STRING }
	 */
	function validUser( $db, $email ) {
		$statement = "SELECT count(id_user) as exist"; 
		$table =  "FROM user "; 
		$condition = "WHERE code_email LIKE '".$_POST["email"]."'"; 
		$request = join( " ", array( $statement, $table, $condition ) ); 

		$results = $db->query($request); 
		$res = $results->fetchArray(SQLITE3_ASSOC);
		
		return $res['exist']; 
	}; /** validUser() */


	/*
	 * Retunr the user parameters 
	 * @params { SQLITE3_DB, STRING }
	 * @return { array[] }
	 */
	function getUserParams( $db, $email ) {
		$statement = "SELECT id_user, status, id_key, date, first_name, last_name, gender"; 
		$table =  "FROM user "; 
		$condition = "WHERE code_email LIKE '".$_POST["email"]."'"; 
		$request = join(" ", array($statement, $table, $condition)); 

		$results = $db->query($request); 

	  $row = array();  
		while ($res = $results->fetchArray(SQLITE3_ASSOC)) {
			foreach ($res as $key => $value) {
				$row[$key] = $value;
			}
			$row['role'] = getStatusRole( $row['status'] ); 
		}	

		return $row; 
	} /** getUserParams() */


	/** Initiate the link to SQLiteDB*/
	$db = new MyDB();

	$i = 0; 
	$res = array(); 
	$res['exist'] = validUser($db, $_POST["email"]); 
	$res['panel'] = array();
	if ($res['exist'] > 0) { /** Only if user already exist in the DB */

		$res['exist'] = true; 
		$res['operator'] = "login"; 
		$user = getUserParams($db, $_POST["email"]); 

		if ($_POST['type'] == "original") { /** Only for index.html */

			/** NAVBAR - User menu*/
			$res['panel'][$i]['parent'] = "ul.navbar-nav"; 
			$res['panel'][$i]['node'] = "li.user-menu#login"; 
			$res['panel'][$i]['last'] = "li.user-menu#logout"; 
			$res['panel'][$i]['action'] = "replaceWith"; 
			$res['panel'][$i]['html'] = '<!-- User Account: style can be found in dropdown.less -->
        <!-- notification - Users\' changes notice -->
        <li class="dropdown messages-menu" id="notification">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell-o"></i>
          </a>
          <ul class="dropdown-menu">
            <li class="header"> Aucun nouvel avis </li>
            <li>
              <!-- inner menu: contains the actual data -->
              <ul class="menu">
                <li>
                  <a href="#">
	                  <h4 style="margin: 0; !important"> 
	                  	Aucun nouvel avis 
	                  	<p class="pull-right"><i class="fa fa-bell text-orange"></i></p>
	                  </h4>
	                  <p style="margin: 0; !important"> En attente de nouvels avis</p>
                  </a>
                </li> 
              </ul>
            </li>
            <li class="footer"><a href="#"> Consultez les tous </a></li>
          </ul>
        </li>
        <!-- User Account: style can be found in dropdown.less -->
				<li class="dropdown user user-menu">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
				    <img src="dist/img/'.getProfileImg($user["gender"]).'" class="user-image" alt="User Image">
				    <span class="hidden-xs">'.$user["first_name"].' '.$user["last_name"].'</span>
				  </a>
				  <ul class="dropdown-menu">
				    <!-- User image -->
				    <li class="user-header">
				      <img src="dist/img/'.getProfileImg($user["gender"]).'" class="img-circle" alt="User Image">
				      <p>
				        '.$user["first_name"].' '.$user["last_name"].'
				        <small>Membre depuis '.getFrenchDate($user["date"]).'</small>
				      </p>
				    </li>
				    <!-- Menu Body -->
				    <li class="user-body">
				      <div class="col-xs-4 text-center">
				        <a href="#"> Groupe</a>
				      </div>
				      <div class="col-xs-4 text-center">
				        <a href="#"> Tâche</a>
				      </div>
				      <div class="col-xs-4 text-center">
				        <a href="#"> IQH</a>
				      </div>
				    </li>
				    <!-- Menu Footer-->
				    <li class="user-footer">
				      <div class="pull-left">
				        <a href="#" class="btn btn-default btn-flat">Profil</a>
				      </div>
				      <div class="pull-right" id="logout">
				        <a href="javascript:" class="btn btn-default btn-flat">De&#769connexion</a>
				      </div>
				    </li>         
				  </ul>
				</li>';
			$i++; 

			/** SIDEBAR - User login short information */ 
			$res['panel'][$i]['parent'] = "section.sidebar"; 
			$res['panel'][$i]['node'] = "div.user-panel#login"; 
			$res['panel'][$i]['last'] = "div.user-panel#logout"; 
			$res['panel'][$i]['action'] = "replaceWith"; 
			$res['panel'][$i]['html'] = '<!-- Sidebar user panel -->
				<div class="user-panel" id="login">
					<div class="pull-left image">
						<img src="dist/img/'.getProfileImg($user["gender"]).'" class="img-circle" alt="User Image">
					</div>
					<div class="pull-left info">
						<p>'.$user["first_name"].' '.$user["last_name"].'</p>
						<a href="#"><i class="fa fa-circle text-success"></i> '.$user["role"].'</a>
					</div>
				</div>'; 
			$i++; 	

			/** SIDEBAR - Landmark tools editor */ 
			$res['panel'][$i]['parent'] = "ul.sidebar-menu"; 
			$res['panel'][$i]['node'] = "li.treeview#editor"; 
			$res['panel'][$i]['action'] = "append"; 
			$res['panel'][$i]['html'] = '<!-- Sidebar Landmark Editor -->
				<li class="treeview" id="editor">
				  <a href="javascript:">
				    <i class="fa fa-pencil"></i> <span> Planification</span>
				    <i class="fa fa-angle-left pull-right"></i>
				  </a>
				  <ul class="treeview-menu" id="editor">
				    <li>
				      <a href="javascript:"><i class="fa fa-circle-o"></i> Inse&#769rer <i class="fa fa-angle-left pull-right"></i></a>
				      <ul class="treeview-menu" id="draw">
				        <li name="Point" onClick="drawFeature(this)"><a href="javascript:"><i class="fa fa-circle-o"></i> Point</a></li>
				        <li name="LineString" onClick="drawFeature(this)"><a href="javascript:"><i class="fa fa-circle-o"></i> Ligne</a></li>
				        <li name="Polygon" onClick="drawFeature(this)"><a href="javascript:"><i class="fa fa-circle-o"></i> Polygone</a></li>
				      </ul>
				    </li>
				    <li>
				      <a href="javascript:"><i class="fa fa-circle-o"></i> Modifer <i class="fa fa-angle-left pull-right"></i></a>
				      <ul class="treeview-menu" id="modify">
				        <li name="select" onClick="drawFeature(this)"><a href="javascript:"><i class="fa fa-circle-o"></i> Se&#769lectionner</a></li>
				        <li name="modify" onClick="drawFeature(this)"><a href="javascript:"><i class="fa fa-circle-o"></i> Remodeler</a></li>
				      </ul>
				    </li>
            <li>
              <a href="javascript:"><i class="fa fa-files-o"></i> Publier <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu" id="publish">
                <li name="select"><a href="javascript:" data-toggle="modal" data-target="#publisher-local"><i class="fa fa-floppy-o"></i> Local </a></li>
                <li name="modify"><a href="javascript:" data-toggle="modal" data-target="#publisher-personal"><i class="fa fa-user"></i> Personnel </a></li>
                <li name="modify"><a href="javascript:" data-toggle="modal" data-target="#publisher-group"><i class="fa fa-group"></i> Groupe </a></li>
                <li name="modify"><a href="javascript:" data-toggle="modal" data-target="#publisher-public"><i class="fa fa-cloud-upload"></i>  Public </a></li>
              </ul>
            </li>
				  </ul>
				</li>'; 
			$i++; 

			/** SIDEBAR - IQH Forms button */ 
			if ( in_array( $user['status'], array( "developpeur", "admin" ) ) ) {
				$res['panel'][$i]['parent'] = "ul.sidebar-menu"; 
				$res['panel'][$i]['node'] = "li.treeview#menu-hsi"; 
				$res['panel'][$i]['action'] = "append"; 
				$res['panel'][$i]['html'] = '<!-- Sidebar Call IQH Forms -->
					<li class="treeview" id="menu-hsi">
					  <a href="javascript">
					    <i class="fa fa-edit"></i> <span> Cre&#769ation d\'IQH</span>
					    <i class="fa fa-angle-left pull-right"></i>
					  </a>
					  <ul class="treeview-menu" id="menu-hsi">
					    <li><a href="javascript:"><i class="fa fa-circle-o"></i> Bleuet sauvage</a></li>
					    <li name="mushroom"><a href="pages/forms/mushroom.html?email='.$_POST["email"].'"><i class="fa fa-circle-o"></i> Champignon forestier</a></li>
					  </ul>
					</li>'; 
				$i++; 
			} 

			/** CONTROL-SIDEBAR - Tag tabs */
			$res['panel'][$i]['parent'] = "ul.control-sidebar-tabs"; 
			$res['panel'][$i]['node'] = "li.info"; 
			$res['panel'][$i]['action'] = "append"; 
			$res['panel'][$i]['responsive'] = "remove"; 
			$res['panel'][$i]['resolution'] = 979; 
			$res['panel'][$i]['html'] = '<li class="info"><a href="#control-sidebar-info-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>'; 
			$i++; 

			/** CONTROL-SIDEBAR - Tag tabs */
			$res['panel'][$i]['parent'] = "div.tab-content"; 
			$res['panel'][$i]['node'] = "div.tab-pane#control-sidebar-info-tab"; 
			$res['panel'][$i]['action'] = "append"; 
			$res['panel'][$i]['responsive'] = "remove"; 
			$res['panel'][$i]['resolution'] = 979; 
			$res['panel'][$i]['after'] = "resetInfo"; 
			$res['panel'][$i]['html'] = '<!-- Info tab content -->
				<div class="tab-pane" id="control-sidebar-info-tab">
					<h3 class="control-sidebar-heading"> Proprie&#769te&#769<small> Balise d\'information</small></h3> 

					<div class="form-group">
					  <label class="control-sidebar-subheading">
					    <i class="fa fa-fw">id</i>&nbsp;&nbsp;
					    <span class="info" id="id" name="Identifiant"> Identifiant</span>
					    <a href="#" id="delete-feature" class="pull-right check" onClick="deleteFeature(this)"><i class="fa fa-fw fa-times-circle"></i></a>
					  </label>
					</div><!-- /.form-group -->  

					<div class="form-group">
					  <label class="control-sidebar-subheading">
					    <i class="fa fa-fw fa-map-pin"></i>&nbsp;&nbsp;
					    <span class="info" id="geom" name="Ge&#769ome&#769trie"> Ge&#769ome&#769trie</span>
					  </label>
					</div><!-- /.form-group -->       

					<div class="form-group">
					  <label class="control-sidebar-subheading">
					    <i class="fa fa-fw fa-object-group"></i>&nbsp;&nbsp;
					    <span class="info" id="text" name="Groupe"> Groupe<small> Nom</small></span>
					    <a href="#" class="pull-right check" data-toggle="modal" data-target="#marker-group-editor"><i class="fa fa-fw fa-check-circle"></i></a>
					  </label>
					</div><!-- /.form-group -->   

					<div class="form-group">
					  <label class="control-sidebar-subheading">
					    <i class="fa fa-fw fa-paint-brush"></i>&nbsp;&nbsp;
					    <span class="info" id="style" name="Symbologie"> Symbologie</span>
					    <a href="#" class="pull-right check" data-toggle="modal" data-target="#marker-style-editor"><i class="fa fa-fw fa-check-circle"></i></a>
					  </label>
					</div><!-- /.form-group -->

					<div class="form-group">
					  <label class="control-sidebar-subheading">
					    <i class="fa fa-fw fa-sticky-note"></i>&nbsp;&nbsp;
					    <span class="info" id="forms" name="Formulaire"> Formulaire</span>
					    <a href="#" class="pull-right check" data-toggle="modal" data-target="#myModal"><i class="fa fa-fw fa-check-circle"></i></a>
					  </label>
					</div><!-- /.form-group -->

				</div><!-- /.tab-pane -->'; 
			$i++; 

		} else if ( $_POST['type'] == "mushroom") { /** Only for mushroom.html */

			if ( in_array( $user['status'], array( "developpeur", "admin" ) ) ) { /** Only if user profil is admin OR developpeur */

				$res['panel'][$i]['parent'] = ".navbar div.container"; 
				$res['panel'][$i]['node'] = "div.navbar-custom-menu"; 
				$res['panel'][$i]['action'] = "append"; 
				$res['panel'][$i]['html'] = '<!-- Navbar User menu -->
					<div class="navbar-custom-menu">
					  <ul class="nav navbar-nav">
					    <!-- User Account: style can be found in dropdown.less --> 
					    <!-- notification - Users\' changes notice -->
			        <li class="dropdown messages-menu">
			          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
			            <i class="fa fa-bell-o"></i>
			          </a>
			          <ul class="dropdown-menu">
			            <li class="header"> Aucun nouvel avis </li>
			            <li>
			              <!-- inner menu: contains the actual data -->
			              <ul class="menu">
			                <li>
			                  <a href="#">
				                  <h4 style="margin: 0; !important"> 
				                  	Aucun nouvel avis 
				                  	<p class="pull-right"><i class="fa fa-bell text-orange"></i></p>
				                  </h4>
				                  <p style="margin: 0; !important"> En attente de nouvels avis</p>
			                  </a>
			                </li> 
			              </ul>
			            </li>
			            <li class="footer"><a href="#"> Consultez les tous </a></li>
			          </ul>
			        </li>
					    <li class="dropdown user user-menu">
					      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
					        <img src="../../dist/img/'.getProfileImg($user["gender"]).'" class="user-image" alt="User Image">
					        <span class="hidden-xs">'.$user["first_name"].' '.$user["last_name"].'</span>
					      </a>
					      <ul class="dropdown-menu">
					        <!-- User image -->
					        <li class="user-header">
					          <img src="../../dist/img/'.getProfileImg($user["gender"]).'" class="img-circle" alt="User Image">
					          <p>
					            '.$user["first_name"].' '.$user["last_name"].'
					        		<small>Membre depuis '.getFrenchDate($user["date"]).'</small>
					          </p>
					        </li>
					        <!-- Menu Body -->
					        <li class="user-body">
					          <div class="col-xs-4 text-center">
					            <a href="#">Followers</a>
					          </div>
					          <div class="col-xs-4 text-center">
					            <a href="#">Sales</a>
					          </div>
					          <div class="col-xs-4 text-center">
					            <a href="#">Friends</a>
					          </div>
					        </li>
					        <!-- Menu Footer-->
					        <li class="user-footer">
					          <div class="pull-left">
					            <a href="#" class="btn btn-default btn-flat">Profil</a>
					          </div>
					          <div class="pull-right">
					            <a href="../../index.html" class="btn btn-default btn-flat">De&#769connexion</a>
					          </div>
					        </li>         
					      </ul>
					    </li>
					   </ul>
					</div>'; 
				$i++; 

			} else { /** Only if user profil isn't admin OR developpeur */
				$res['type'] = "warning";
				$res['title'] = "Droit d'acce&#768s re&#769voqu&#769"; 
				$res['text'] = "Vous n'e&#770tes pas autorise&#769 a&#768 naviguer sur cette page."; 	
			}
		}

	} else { /** Only if user not already exist in the DB */

		if ( $_POST['type'] == "mushroom" ) { /** Only for mushroom.html */
			$res['exist'] = false; 
			$res['type'] = "danger";
			$res['title'] = "Connexion e&#769choue&#769"; 
			$res['text'] = "Vous n'e&#770tes pas autorise&#769 a&#768 naviguer sur cette page."; 	
		}
		
	} 

	echo json_encode($res); 

?>