<?php

	#$_POST["email"] = "olivier.dupras-tessier@usherbrooke.ca"; 
	#$_POST["password"] = "jamais3Fois"; 
	#$_POST["type"] = "original"; 


	class MyDB extends SQLite3 {
    function __construct() {
      $this->open('mysqlitedb.db');
    }
	}


	/*
	 * Get the real employed function name of a status 
	 * @params { STRING }
	 * @return { STRING }
	 */
	function getStatusRole( $obj ) {
		if ($obj == "membre") {
			$results = "Membre";
		} else if ( $obj == "developpeur" ) {
			$results = "De&#769veloppeur";
		} else if ( $obj == "admin" ) {
			$results = "Administrateur";
		}

		return $results; 
	} /** getStatusRole */


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
	} /** getFrenchDate */


	/** 
	 * Define profil picture from user gender
	 * @params {STRING}
	 */ 
	function getProfileImg($obj) {	
		if ($obj == "male") {
			$results = "avatar5.png"; 
		} else if ($obj == "female") {
			$results = "avatar2.png"; 
		}

		return $results; 
	}; /** getProfileImg */


	$db = new MyDB();
	$statement = "SELECT id_user, status, id_key, date, first_name, last_name, gender"; 
	$table =  "FROM user "; 
	$condition = "WHERE code_email LIKE '".$_POST["email"]."' AND code_password LIKE '".$_POST["password"]."'"; 
	$request = join(" ", array($statement, $table, $condition)); 

	$results = $db->query($request); 

	/** Fetch results from SQLite request */
  $row = array(); 
	while ($res = $results->fetchArray(SQLITE3_ASSOC)) {
		$row['id_user'] = $res['id_user']; 
		$row['status']  = $res['status']; 
		$row['id_key']  = $res['id_key']; 
		$row['date'] = $res['date']; 
		$row['first_name'] = $res['first_name']; 
		$row['last_name'] = $res['last_name']; 
		$row['gender'] = $res['gender']; 
		$row['role'] = getStatusRole( $row['status'] ); 
	}	

	/** Create html content by user status */
	$i = 0; 
	$res = array(); 
	$res['panel'] = array();
	if (isset($row['id_user'])) { 

		/** MODAL - Layout & Content builder */
		$res['exist'] = true; 
		$res['type'] = "success";
		$res['title'] = "Connexion e&#769tablie"; 
		$res['operator'] = "login"; 
		$res['text'] = "Votre compte d'usager a&#768 e&#769te&#769 charge&#769 avec suce&#768s.<br> Profitez des outils et des composantes mises a&#768 votre disposition."; 

		if ($_POST['type'] == "original") {

			/** NAVBAR - User menu*/  #<i class="fa fa-bell text-yellow"></i> Aucun nouvel avis 
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
				<li class="dropdown user user-menu">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
				    <img src="dist/img/'.getProfileImg( $row["gender"] ).'" class="user-image" user="'. $_POST["email"] .'" alt="User Image">
				    <span class="hidden-xs" id="user_name">'.$row["first_name"].' '.$row["last_name"].'</span> 
				  </a>
				  <ul class="dropdown-menu" id="loged">
				    <!-- User image -->
				    <li class="user-header">
				      <img src="dist/img/'.getProfileImg( $row["gender"] ).'" class="img-circle" alt="User Image">
				      <p>
				        '.$row["first_name"].' '.$row["last_name"].'
				        <small>Membre depuis '.getFrenchDate( $row["date"] ).'</small>
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
						<img src="dist/img/'.getProfileImg($row["gender"]).'" class="img-circle" alt="User Image">
					</div>
					<div class="pull-left info">
						<p>'.$row["first_name"].' '.$row["last_name"].'</p>
						<a href="#"><i class="fa fa-circle text-success"></i> '.$row["role"].'</a>
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
				      <ul class="treeview-menu" id="draw" layer="draw">
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
			if (in_array($row['status'], array("developpeur", "admin"))) {
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
			$res['panel'][$i]['html'] = '<li class="info"><a href="#control-sidebar-info-tab" data-toggle="tab"><i class="fa fa-pencil"></i></a></li>'; 
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

			/** CONTROL-SIDEBAR - Tag tabs */
			$res['panel'][$i]['parent'] = "ul.control-sidebar-tabs"; 
			$res['panel'][$i]['node'] = "li.info"; 
			$res['panel'][$i]['action'] = "append"; 
			$res['panel'][$i]['responsive'] = "remove"; 
			$res['panel'][$i]['resolution'] = 979; 
			$res['panel'][$i]['html'] = '<li class="load"><a href="#control-sidebar-load-tab" data-toggle="tab"><i class="fa fa-cloud-upload"></i></a></li>'; 
			$i++; 

			/** CONTROL-SIDEBAR - Tag tabs */
			$res['panel'][$i]['parent'] = "div.tab-content"; 
			$res['panel'][$i]['node'] = "div.tab-pane#control-sidebar-load-tab"; 
			$res['panel'][$i]['action'] = "append"; 
			$res['panel'][$i]['responsive'] = "remove"; 
			$res['panel'][$i]['resolution'] = 979; 
			$res['panel'][$i]['after'] = "resetInfo"; 
			$res['panel'][$i]['html'] = '<!-- Load tab content -->
				<div class="tab-pane" id="control-sidebar-load-tab">
	        <h3 class="control-sidebar-heading"> Gestionnaire de projets</small></h3> 

	        <div class="box box-primary" id="load">
	          <div class="box-header">
	            <div class="box-tools pull-right">
	              <button class="btn btn-box-tool" id="save-project" rel="tooltip" data-title="Enregistrer" disabled>
	                <i class="fa fa-check-circle text-green"></i>
	              </button>
	              <button class="btn btn-box-tool" id="clear-project" rel="tooltip" data-title="Débarasser" disabled>
	                <i class="fa fa-times-circle text-red"></i>
	              </button>
	              <button class="btn btn-box-tool" data-toggle="modal" data-target="#load-project" rel="tooltip" data-title="Charger">
	                <i class="fa fa-plus-circle text-blue"></i>
	              </button>
	            </div> 
	          </div>
	          <div class="box-body text-black">
	          	<div class="span-12">
		            <table class="table table-hover table-bordered table-striped" id="load" width="100%">
		              <thead>
		                <tr>
		                  <th>#</th>
		                  <th>Projet</th>
		                </tr>
		              </thead>
		              <tbody>
		              </tbody>
		            </table> 
		          </div>
	          </div><!-- /.box-body -->
	          <div class="box-footer">
	          	<div class="row">
	          		<div class="col-xs-offset-2 col-xs-9">
	          			<div class="clearfix">
	          				<span class="pull-left">original</span>
	          				<small class="pull-right"><i class="fa fa-user"></i> - 0</small>
	          			</div>
	          		</div>
	          	</div>
	          </div>
	        </div><!-- /.box #age-->

	      </div><!-- /.tab-pane#control-sidebar-load-tab -->'; 
      $i++; 

		} 

	} else {

		/** MODAL - Layout & Content builder */
		$res['exist'] = false; 
		$res['type'] = "warning"; 
    $res['title'] = "Connexion e&#769choue&#769e"; 
    $res['text'] = "Ve&#769rifiez que votre adresse courriel ou votre mot de passe soient bien e&#769crit.<br> Autrement, demandez a&#768 <b>Devenir membre</b>."; 

	} 

	echo json_encode($res); 

?>