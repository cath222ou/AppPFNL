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
		$res['text'] = "Votre compte d'usager a&#768 e&#769te&#769 charge&#769 avec succe&#768s.<br> Profitez des outils et des composantes mises a&#768 votre disposition."; 

		if ($_POST['type'] == "original") {

			/** NAVBAR - User menu*/  #<i class="fa fa-bell text-yellow"></i> Aucun nouvel avis 
			$res['panel'][$i]['parent'] = "ul.navbar-nav"; 
			$res['panel'][$i]['node'] = "li.user-menu#login"; 
			$res['panel'][$i]['last'] = "li.user-menu#logout"; 
			$res['panel'][$i]['action'] = "replaceWith"; 
			$res['panel'][$i]['html'] = '<!-- User Account: style can be found in dropdown.less -->
        
				<li class="dropdown user user-menu">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
				    <img src="iqh/dist/img/'.getProfileImg( $row["gender"] ).'" class="user-image" user="'. $_POST["email"] .'" alt="User Image">
				    <span class="hidden-xs" id="user_name">'.$row["first_name"].' '.$row["last_name"].'</span> 
				  </a>
				  <ul class="dropdown-menu" id="loged">
				    <!-- User image -->
				    <li class="user-header">
				      <img src="iqh/dist/img/'.getProfileImg( $row["gender"] ).'" class="img-circle" alt="User Image">
				      <p>
				        '.$row["first_name"].' '.$row["last_name"].'
				        <small>Membre depuis '.getFrenchDate( $row["date"] ).'</small>
				      </p>
				    </li>
				    <!-- Menu Body -->
				    <!--li class="user-body">
				      <div class="col-xs-4 text-center">
				        <a href="#"> Groupe</a>
				      </div>
				      <div class="col-xs-4 text-center">
				        <a href="#"> Tâche</a>
				      </div>
				      <div class="col-xs-4 text-center">
				        <a href="#"> IQH</a>
				      </div>
				    </li-->
				    <!-- Menu Footer-->
				    <li class="user-footer">
				      <!--div class="pull-left">
				        <a href="#" class="btn btn-default btn-flat">Profil</a>
				      </div-->
				      <div class="pull-right" id="logout" data-toggle="tooltip" data-title="De&#769connexion">
				        <!--a href="javascript:" class="btn btn-default btn-flat">
				        	<i class="fa fa-sign-out"></i>
				        </a-->
				        <button type="button" class="btn btn-primary btn-block btn-flat online" data-toggle="tooltip" data-title="Terminer" onClick="window.location.reload()">
                  <i class="fa fa-sign-out"></i>
                </button>
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
				<div class="user-panel" id="login" status="'.$row["role"].'" online="true" >
					<div class="pull-left image">
						<img src="iqh/dist/img/'.getProfileImg($row["gender"]).'" class="img-circle" alt="User Image">
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
				
				    
				  
				
				  <ul class="treeview-menu" id="editor">
				    <li>
				      Inse&#769rer <a href="javascript:"></a>
				      <ul class="treeview-menu" id="draw" layer_name="draw" layer_id="0" layer_color="#ffcc33">
				        <li name="Point" onClick="drawFeature(this)" style="display: inline-block; "><a href="javascript:"><i class="fa fa-circle-o"></i> Point</a></li>
				        <li name="LineString" onClick="drawFeature(this)" style="display: inline-block; "><a href="javascript:"><i class="fa fa-circle-o"></i> Ligne</a></li>
				        <li name="Polygon" onClick="drawFeature(this)" style="display: inline-block; "><a href="javascript:"><i class="fa fa-circle-o"></i> Polygone</a></li>
				      </ul>
				    </li>
				    <li>
				      Modifer
				      <ul class="treeview-menu" id="modify">
				        <li name="select" onClick="drawFeature(this)" style="display: inline-block; "><a href="javascript:"><i class="fa fa-circle-o"></i> Se&#769lectionner</a></li>
				        <li name="modify" onClick="drawFeature(this)" style="display: inline-block; "><a href="javascript:"><i class="fa fa-circle-o"></i> Remodeler</a></li>
				      </ul>
				    </li>
                    <li class="publish-menu online">
                    Publier   
                    <ul class="treeview-menu" id="publish">
                        <li name="local" style="display: inline-block; "><a href="javascript:" data-toggle="modal" data-target="#publisher-local"><i class="fa fa-floppy-o"></i> Local </a></li>
                        <li name="personal" style="display: inline-block; "><a href="javascript:" data-toggle="modal" data-target="#publisher-personal"><i class="fa fa-user"></i> Personnel </a></li>
                    </ul>
                    </li>
				  </ul>
				</li>'; 
			$i++; 

            $res['panel'][$i]['parent'] = "panel"; 
			     $res['panel'][$i]['node'] = "#start_outils"; 
			     $res['panel'][$i]['action'] = "show";
                 $i++;
            
            /** Partie Olivier **/
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
        <h3 class="control-sidebar-heading"> Proprie&#769te&#769 des entite&#769s</h3> 

          <div class="box box-widget " id="project-info">
          	<div class="box-header with-border">
							<h3 class="box-title">Projet</h3>
            	<div class="box-tools">
	              <button class="btn btn-box-tool" id="modify" name="select" onClick="drawFeature(this)" rel="tooltip" title="Se&#769lection">
	                <i class="fa fa-mouse-pointer"></i>
	              </button>
	              <button class="btn btn-box-tool" id="position" name="Point" onClick="drawFeature(this)" rel="tooltip" title="Position">
	                <i class="fa fa-map-marker"></i>
	              </button>	 
	              <i class="fa fa-w split" >|</i>
	              <button class="btn btn-box-tool" id="position" name="LineString" status="stop" onClick="drawFeature(this)" rel="tooltip" title="Suivi">
	                <i class="fa fa-location-arrow"></i>
	              </button>	 
	              <i class="fa fa-w split" >|</i>
	              <button class="btn btn-box-tool" id="zoom-project" rel="tooltip" title="E&#769tendue">
	                <i class="fa fa-search"></i>
	              </button>
	            </div> 
						</div>
            <div class="box-body no-padding">
              <table class="project-table-info table table-condensed text-black text-center">
                <tr>
                  <td id="pId">0</td>
                  <td id="pName">original</td>
                  <td id="pType">
                    <span class="badge" style="background-color: #ffcc33;">
                      <i class="fa fa-user text-white"></i>
                    </span>
                  </td>
                </tr>
              </table>
            </div><!-- /.box-body -->
          </div><!-- /.box --> 

          <div class="box box-widget" id="feature-info">
						<div class="box-header with-border">
							<h3 class="box-title">Entite&#769s</h3>
	            <div class="box-tools">
	              <button class="btn btn-box-tool" id="last-feature" rel="tooltip" title="Pre&#769ce&#769dent">
	                <i class="fa fa-caret-left"></i>
	              </button>
	              <button class="btn btn-box-tool" id="next-feature" rel="tooltip" title="Suivant">
	                <i class="fa fa-caret-right"></i>
	              </button>
	            </div> 
	          </div>
	          <div class="box-body no-padding">
              <table class="table table-condensed text-black">
                <tr class="text-center" id="fid">
                  <td style="width: 40px"><i class="fa fa-w">ID</i></td>
                  <td id="info" style="width: 150px">Identifiant</td> 
                <tr class="text-center" id="geom">
                  <td style="width: 40px"><i class="fa fa-w fa-map-pin"></i></td>
                  <td id="info" style="width: 150px">Ge&#769ome&#769trie</td> 
                </tr>
                <tr class="text-center" id="group">
                  <td style="width: 40px"><i class="fa fa-w fa-tag"></i></td>
                  <td id="info" style="width: 150px">Groupe</td> 
                </tr> 
                <tr class="text-center" id="label">
                  <td style="width: 40px"><i class="fa fa-w fa-commenting"></i></td>
                  <td id="info" style="width: 150px">E&#769tiquette</td> 
                  <td id="modal" style="width: 10px">
                    <a href="#" class="pull-right check" name="defaut" id="label" rel="tooltip" data-title="Modifier" onClick="setModalLabel( this )"><i class="fa fa-fw fa-check-circle text-green"></i></a>
                  </td>
                </tr> 
                <tr class="text-center" id="style">
                  <td style="width: 40px"><i class="fa fa-w fa-paint-brush"></i></td>
                  <td id="info" style="width: 150px">
                    <span class="badge" style="background-color:#ffcc33; font-color:#ffffff;">
                      Symbologie
                    </span>
                  </td>
                  <td id="modal" style="width: 10px">
                    <a href="#" class="pull-right check" name="defaut" id="style" data-toggle="modal" data-target="#marker-style-editor" rel="tooltip" data-title="Modifier"><i class="fa fa-fw fa-check-circle text-green"></i></a>
                  </td>
                </tr>
                <tr class="text-center" id="forms">
                  <td style="width: 40px"><i class="fa fa-w fa-shopping-cart"></i></td>
                  <td id="info" style="width: 150px">Productivite&#769</td>
                  <td id="modal" style="width: 10px">
                    <a href="#" class="pull-right check" name="validation" id="bluberry" modal-target="marker-valid-blueberry" rel="tooltip" data-title="Modifier" onClick="featureProperties2validForms( this )">
                    	<i class="fa fa-fw fa-check-circle text-green"></i>
                    </a>
                  </td>
                </tr>
                <tr class="text-center" id="forest">
                  <td style="width: 40px"><i class="fa fa-w fa-tree"></i></td>
                  <td id="info" style="width: 150px">E&#769cosyste&#768me</td>
                  <td id="modal" style="width: 10px">
                    <a href="#" class="pull-right check" name="validation" id="forest" modal-target="marker-valid-forest" rel="tooltip" data-title="Modifier" onClick="featureProperties2validForms( this )"><i class="fa fa-fw fa-check-circle text-green"></i></a>
                  </td>
                </tr>
              </table>
            </div><!-- /.box-body --> 
            <div class="box-footer">
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" id="delete-feature" rel="tooltip" data-title="Supprimer" onClick="deleteFeature( this )" disabled>
                  Effacer l\'entite&#769&nbsp;&nbsp;<i class="fa fa-times-circle text-red"></i>
                </button>
              </div> 
            </div><!-- /.box-footer -->
          </div><!-- /.box -->

        </div><!-- /.tab-pane -->'; 
			$i++; 

			/** CONTROL-SIDEBAR - Tag tabs */
			$res['panel'][$i]['parent'] = "ul.control-sidebar-tabs"; 
			$res['panel'][$i]['node'] = "li.load"; 
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

	        <div class="box box-widget" id="load">
	          <div class="box-header">
	          	<h3 class="box-title"> Liste</h3>
	            <div class="box-tools pull-right">
	              <button class="btn btn-box-tool online" id="remove-project" name="project" rel="tooltip" data-title="Retirer">
	                <i class="fa fa-minus-circle text-blue"></i>
	              </button>
	              <button class="btn btn-box-tool online" id="get-project" name="project" data-toggle="modal" data-target="#load-project" rel="tooltip" data-title="Charger">
	                <i class="fa fa-plus-circle text-blue"></i>
	              </button>
	            </div> 
	          </div>
	          <div class="box-body no-padding text-black">
	          	<div class="span-12">
		            <table class="table display" cellspacing="0" id="load" width="100%">
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
	            <div class="box-tools pull-right">
	              <button class="btn btn-box-tool online" id="save-project" name="project" rel="tooltip" data-title="Enregistrer" disabled>
	                <i class="fa fa-check-circle text-green"></i>
	              </button>
	              <button class="btn btn-box-tool online" id="delete-project" name="project" rel="tooltip" data-title="Supprimer" disabled>
	                <i class="fa fa-times-circle text-red"></i>
	              </button>
	            </div>
	          </div>
	        </div><!-- /.box #load -->

	        <div class="box box-widget" id="comments">
	          <div class="box-header">
	          	<h3 class="box-title"> Commentaire</h3>
	            <div class="box-tools pull-right">
	              <button class="btn btn-box-tool" id="modify-comments" data-toggle="modal" data-target="#project-comments" rel="tooltip" data-title="Modifier" disabled>
	                <i class="fa fa-check-circle text-green"></i>
	              </button>
	            </div>
	          </div>
		        	<div class="box-body text-black" id="comments-box" style="overflow: hidden; width: auto; height: 100px;"> 
		            <p>...</p>
			        </div><!-- /.box-body -->
	        </div><!-- /.box #load -->

	      </div><!-- /.tab-pane#control-sidebar-load-tab -->'; 
      $i++; 
            
            /** SIDEBAR - Menu publish group */ 
			if ( in_array( $row['status'], array( "developpeur", "admin" ) ) ) {
				$res['panel'][$i]['parent'] = ".box#load .box-footer .box-tools"; 
				$res['panel'][$i]['node'] = "button#stop-sharing"; 
				$res['panel'][$i]['action'] = "prepend"; 
				$res['panel'][$i]['html'] = '<button class="btn btn-box-tool online pull-left" id="stop-sharing" utility="share" name="project" rel="tooltip" data-title="Arre&#770ter le partage" disabled>
					<i class="fa fa-user-times text-blue"></i>
	      </button>'; 
				$i++; 

				$res['panel'][$i]['parent'] = ".box#load .box-footer .box-tools"; 
				$res['panel'][$i]['node'] = "button#share-project"; 
				$res['panel'][$i]['action'] = "prepend"; 
				$res['panel'][$i]['html'] = '<button class="btn btn-box-tool online pull-left" id="share-project" utility="share" name="project" rel="tooltip" data-title="Activer le partage" disabled>
					<i class="fa fa-user-plus text-blue"></i>
	      </button>'; 
				$i++; 
			} 
            
            
            /** Fin partie Olivier **/
            
            
            /** Mickael : Si développeur - créer Outils IQH */

            if (($row['role'] == "De&#769veloppeur")||($row['role'] == "Administrateur")){
			     $res['panel'][$i]['parent'] = "panel"; 
			     $res['panel'][$i]['node'] = "#start_filter"; 
			     $res['panel'][$i]['action'] = "show";
                 $i++;
                 $res['panel'][$i]['parent'] = "panel"; 
			     $res['panel'][$i]['node'] = "#start_filter";
                 $res['panel'][$i]['info'] = 'iqh/pages/blueberry_v10.html?email='.$_POST["email"];
			     $res['panel'][$i]['action'] = "link";
                 $i++;
            }

        }
        
        
       $statement = "SELECT id_iqh"; 
	   $table =  "FROM iqh "; 
	   $condition = "WHERE id_user = ".$row['id_user']; 
	   $request = join(" ", array($statement, $table, $condition)); 
    
       
	   $results = $db->query($request); 

	
        $num = 0;
        $iqh = array();
        
	   while ($resiqh = $results->fetchArray(SQLITE3_ASSOC)) {
		$iqh[$num] = $resiqh['id_iqh']; 
       
           $res['panel'][$i]['parent'] = "panel";
        $res['panel'][$i]['node'] = "#catalog"; 
        $res['panel'][$i]['action'] = "append2";
        $res['panel'][$i]['html'] = '<ul class="group">
									<a id="couchedefond" class="panel-heading accordion-toggle collapsed" data-toggle="collapse"  href="#IQH2"  aria-expanded="false"> 
										<i class="visible-collapsed fa fa-plus-square-o"></i> 
										<i class="hidden-collapsed fa fa-minus-square-o"></i> 
									</a> 
									<li id="layer3" >
										<label class="checkbox" for="visible3">
											<input id="visible3" class="visible" type="checkbox"/>Bleuet IQH #'.$iqh[$num].'
										</label>
									</li>
								</ul> 
								<ul id="IQH2" class="layertree collapse">
                                    <li id="layer33">
										<label class="checkbox" for="visible33">
											<input id="visible33" class="visible" type="checkbox"/>IQH WMS
										</label>
										<fieldset class="layer">
											<div class="col-md-4">Opacité</div>
											<input class="opacity col-md-8" type="range" min="0" max="1" step="0.01"/>
										</fieldset>
									</li>
                            </ul> ';
          $i++;
            $num++;
            
	   }
       
       
       
           #error_log($res['panel'][$i]['html']); 
           
         #$res['max']="$iqh[$num]";
        
	}
    

    else {

		/** MODAL - Layout & Content builder */
		$res['exist'] = false; 
		$res['type'] = "warning"; 
    $res['title'] = "Connexion e&#769choue&#769e"; 
    $res['text'] = "Ve&#769rifiez que votre adresse courriel ou votre mot de passe soient bien e&#769crit.<br> Autrement, demandez a&#768 <b>Devenir membre</b>."; 

	} 
    
	echo json_encode($res); 

?>