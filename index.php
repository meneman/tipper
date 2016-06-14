<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Tipper</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles    <link href="/assets/css/bootstrap.css" rel="stylesheet"> -->
   
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">
      

    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="assets/css/costom.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

      

<?php       
   session_start();
    include_once('ajax/config.php');
    $error = array();
    if(@!$_SESSION['login'] AND isset($_POST['job'])) {
            if($_POST['job'] == 'create'){
                $name = $_POST['sessionname'];
                $pw = $_POST['password'];
                $pw2 = $_POST['confirm-password'];
                if($pw != $pw2){
                    $error[] = 'Passwörter sind unterschiedlich';
                }
                if(strlen($pw) == 0){
                    $error[] = 'Kein Passwort angegeben';
                }
                if(!isset($error[0])){
                $rs = (bool) $mysqli->query("SELECT EXISTS (SELECT 1 FROM sessions WHERE session_name = '".$name."')")->fetch_row()[0];
		  echo $rs;
                if($rs) {
                    $error[] = 'Dieser Sessionname existiert bereits';
                    }
                    
                if(!isset($error[0])){
                    $passwort_hash = password_hash($pw, PASSWORD_DEFAULT);
                    
                    $result = $mysqli->query("INSERT INTO `sessions` (`id`, `session_name`, `password`, `login_tries`) VALUES (NULL, '".$name."', '".$passwort_hash."', '0');");
                    $message = "Eine Neue Session wurde angelegt";
                }
                
	           }    
                
                
                
                
            } elseif($_POST['job'] == 'login') {
                
                $name = $_POST['sessionname'];
                $pw = $_POST['password'];
                // auslesen einer mysql zeiel
                $session = $mysqli->query("SELECT 1 FROM sessions WHERE session_name = '".$name."'")->fetch_row();
                if( ((bool) $mysqli->query("SELECT EXISTS (SELECT 1 FROM sessions WHERE session_name = '".$name."')")->fetch_row()[0]) AND password_verify($pw, $session['passwort']) ){
                    
                    
                    $_SESSION['sessionID'] = $user['id'];
                    $_SESSION['login'] =true;
                } else {
                    $error[] = "Password oder Nutzername falsch";
                    $_SESSION['login'] = false;
                }
            }

    } elseif(@!$_SESSION['login'] AND !isset($_POST['job'])) { 
        $_SESSION['login'] = false;
    } 
        
    

?>
        
      <div class="container">      
   <?php 
        if($_SESSION['login']){ 
    ?>
        



        
        
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Tipper</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
      <ul class="nav navbar-nav"> 
        <li ><a href="#" data-toggle="modal" data-target="#newPlayer">neuen Spieler </a></li>  
        <li ><a href="#" data-toggle="modal" data-target="#deletePlayer">Spieler löschen </a></li>
        <li ><a href="#" data-toggle="modal" data-target="#newPlayer">alle bets leeren </a></li>
        <li></li>
 
      </ul>

    
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

        
        

<div class="panel panel-info">
       <div class="panel panel-heading">
        <h5 class"panel-title"> Bestenliste</h3>   
     </div> 
    
        <div id="ranking"></div>
        
</div>

 <div class="panel panel-primary">
     <div class="panel panel-heading">
        <h5 class"panel-title"> anstehende Spiele</h3>   
     </div>
 <ul class="list-group">
     
     <div id="upcomingGames"></div>

     <li class=list-group-item>
        <div class="btn-group" role="group" >
         <button type="button" class="btn btn-success btn-lg disabled"><span class="glyphicon glyphicon-plus" aria-hidden="true"></button>
         <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#newGame"></span> neues Spiel hinzufügen</button>
         </div>
         
     </li>
  </ul>
</div>       

        

 <div class="panel panel-primary">
     <div class="panel panel-heading">
        <h5 class"panel-title"> History</h3>   
     </div>
         <table class="table table-striped">
        <thead>
            <tr>
                <td>Datum</td><td>Spiel</td><td>Ergebnis</td>
            </tr>
        </thead>
        <tbody>
            
        <div class="historyGames"></div>
        </tbody>
      </table>    
</div>       

        

        

      
      <!-- New Game Modal -->
<div class="modal fade" id="newGame" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        
        <form id="newGameForm" action="ajax/updateTable.php" method="POST">
                    
                    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"> Spiel hinzufügen</h4>
      </div>
      <div class="modal-body">
                
            <form>
              <div class="form-group">
                <label for="teamOne">Mannschaft 1:</label>
                <input type="text" name="mannschaftone" class="form-control" id="mannschaftone" placeholder="Deutschland">
              </div>
                
              <div class="form-group">
                <label for="teamTwo">Mannschaft 2:</label>
                <input type="text" name="mannschafttwo" class="form-control" id="mannschafttwo" placeholder="Russland">
              </div>

                
              <div class="checkbox">
                <label>
                  <input type="checkbox"> Heimspiel
                </label>
              </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">schließen</button>
        <button type="submit"  class="btn btn-primary">hinzufügen</button>
      </div>
            
            
        <input type="hidden" name="job" value="addGame">   
        </form>
    </div>
  </div>
</div>
    
    
    <!-- New Player Modal -->
<div class="modal fade" id="newPlayer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        
        <form id="newPlayerForm" action="ajax/updateTable.php" method="POST">
        
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Neuer Spieler</h4>
      </div>
      <div class="modal-body">
             
            
              <div class="form-group"
                  name="firstname">
                <label for="mannschaftone">Name</label>
                <input type="text" name="InputName" class="form-control" id="mannschaftone" placeholder="Gustav">
              </div>
            
        
              <div class="checkbox">
                <label>
                  <input type="checkbox"> Ist der Spieler dick?
                </label>
              </div>
            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">schließen</button>
        <button type="submit" class="btn btn-primary">Spieler hinzufügen</button>
      </div>
        
        <input type="hidden" name="job" value="addPlayer">   
        </form>
      
    </div>
  </div>
</div>  


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">New message</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>
          <div class="form-group">
            <label for="message-text" class="control-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send message</button>
      </div>
    </div>
  </div>
</div>
        
        
        
    
    <!-- Delete Player Modal -->
<div class="modal fade" id="deletePlayer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        
        <form id="deletePlayerForm" action="ajax/updateTable.php" method="POST">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Spieler - löschen</h4>
      </div>
      <div class="modal-body">
             
            
              <div class="form-group">
                <label for="selectedPlayer"></label>
                <select multiple class="form-control" class="playerSelectForm" name="selectedPlayer" id="playerSelectFormDelete">

                </select>
              </div>
            
    
            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">schließen</button>
        <button type="submit" class="btn btn-danger">Spieler löschen</button>
      </div>
            
        <input type="hidden" name="job" value="deletePlayer">         
        </form>
      
    </div>
  </div>
</div>  

      <!-- Dynamic Modal Set Score -->
<div class="modal fade"  id="SetScoreModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    
  <div class="modal-dialog" role="document">
      
    <div class="modal-content">
        
       <form  id="addResult" action="ajax/updateTable.php" method="POST"> 
           
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="setScoreLable">Neuer Spieler</h4>
      </div>
        
        
        
      <div class="modal-body">
          <h4>Ergebniss: </h4>
            <div class="controls form-inline">
                    
                <div class="SetScoreInputs"></div>
                
                <input type="text" name="InputName" class="form-control" id="mannschaftone" >
        
            

                
                <input type="text" name="InputName" class="form-control" id="mannschafttwo" placeholder="Gustav">

            
          </div>
                
            

      </div>
        
        
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" type="button" class="btn btn-primary">Save changes</button>
      </div>
           
        <input type="hidden" name="job" value="addResult">  
        </form>  
    </div>
 
    </div>
    
</div>  
    
    
    
    <!-- Dynamic Modal Tipping  -->
<div class="modal fade"  id="TipGameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    
  <div class="modal-dialog" role="document">
      
    <div class="modal-content">
        
       <form  id="makeBetForm" action="ajax/updateTable.php" method="POST"> 
           
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="setScoreLable">Tippen</h4>
      </div>
        
        
        
      <div class="modal-body">
          <h4> Tip abgeben: </h4>
          
          
              <div class="form-group">
                <label for="selectedPlayer"></label>
                <select multiple class="form-control" class="playerSelectForm" name="selectedPlayer" id="playerSelectFormTip">

                </select>
              </div>
          
            <div class="controls form-inline">
                    
                
                
                <input type="text" name="InputName" class="form-control" id="tiptone" >
        
            

                
                <input type="text" name="InputName" class="form-control" id="tiptwo" >

            
          </div>
                
            

      </div>
        
        
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" type="button" class="btn btn-primary">Save changes</button>
      </div>
           
        <input type="hidden" name="job" value="tipGame">  
        </form>  
    </div>
 
    </div>
    
</div>  
    

    
    
       <?php 
        } else { 
            if(isset($error[0])){
                foreach($error as $key => $value){
                echo "<div class='alert alert-warning' role='alert'>".$value."</div>";
                }
            } else if(isset($message)){
                echo "<div class='alert alert-success' role='alert'>".$message."</div>";
            }
    ?>
     

<div class="container">
    	<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-6">
								<a href="#" class="active" id="login-form-link">Login</a>
							</div>
							<div class="col-xs-6">
								<a href="#" id="register-form-link">Register</a>
							</div>
						</div>
						<hr>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form id="login-form" action="index.php" method="post" role="form" style="display: block;">
									<div class="form-group">
										<input type="text" name="sessionname" id="sessionname" tabindex="1" class="form-control" placeholder="Session" value="">
									</div>
									<div class="form-group">
										<input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6 col-sm-offset-3">
												<input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
											</div>
										</div>
									</div>
 <input type="hidden" name="job" value="login"> 
								</form>
								<form id="register-form" action="index.php" method="post" role="form" style="display: none;">
                                    <h4> Neue Session anlegen</h4>
									<div class="form-group">
										<input type="text" name="sessionname" id="sessionname" tabindex="1" class="form-control" placeholder="Name" value="">
									</div>
									<div class="form-group">
										<input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
									</div>
									<div class="form-group">
										<input type="password" name="confirm-password" id="confirm-password" tabindex="2" class="form-control" placeholder="Password bestätigen">
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6 col-sm-offset-3">
												<input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Session erstellen">
											</div>
										</div>
									</div>
                                    <input type="hidden" name="job" value="create">  
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<script>


</script>

           <?php 
        } // endif
        ?>    

          </div>
      
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="assets/js/jquery-1.12.3.min.js"></script>
    
    
    <!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    
    
    <script src="assets/js/jquery.form.js"></script>
    
      
    <script>
        
        $(document).ready(function() {

        
            $("#3").on('click', function(e) {
   $modal.modal('toggle', $(this));
});
            
            
            $('#newPlayerForm').ajaxForm(function() { 
            init();
               
            }); 
            
            $('#deletePlayerForm').ajaxForm(function() { 
            init();      

            }); 
                        
            $('#newGameForm').ajaxForm(function() { 
            init();      

            }); 
                                    
            $('#addResult').ajaxForm(function() { 
            init();      

            }); 
                                    
            $('#makeBetForm').ajaxForm(function() { 
            init();      

            }); 

            function init(){
             $.get('ajax/list.ajax.php', function(data) {
                
                AllData =$.parseJSON(data);
				console.log(AllData);
                buildHtmlTable(AllData.spieler);
                buildGamesList(AllData.matches);
                buildPlayerSelectForm(AllData.spieler);
                return AllData;
            });
            }
            
            
            function buildHtmlTable(playerlist) {
                console.log(playerlist);
                //remove old Table
                $('#ranking').children().remove();
                // create table
                var $table = $('<table>').addClass('table table-striped')

                // thead
                $table
                .append('<thead>').children('thead')
                .append('<tr />').children('tr').append('<th>Name</th><th>Score</th><th>tips</th>');

                //tbody
                var $tbody = $table.append('<tbody />').children('tbody');

                    var dicklabel;
                $.each(playerlist, function (i, player) {
                    if(player[2] == 1){
                       dicklabel = "<span class='label label-danger'>fett "+" "+" <span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span>"
                    } else {
                        dicklabel = "";
                    }
                $tbody.append('<tr />').children('tr:last')
                .append("<td>"+player[0]+" "+dicklabel+" </td>")
                .append("<td>"+ player[1]+"</td>")
                .append("<td> "+player[2]+"</td>");


                });


                // add table to dom
                $table.appendTo('#ranking');	
            }
            
            
            
            function buildGamesList(games){
                $('#upcomingGames').children().remove();
                $('#historyGames').children().remove();
                
                
                var upcomingItems = [];
                var historyItems = [];
            $.each(games, function(i, match)   {
                
                if(match.finished == 0){
                upcomingItems.push("<li class=list-group-item>"+match.name_first+" vs. "+match.name_secound+" Datum: "+match.spieldatum+"                   <button data-toggle=modal data-target=#TipGameModal data-match="+i+" class='btn btn-warning btn-small'  id="+i+">tippen</button> <button data-toggle=modal data-target=#SetScoreModal data-match="+i+" class='btn btn-warning btn-small tipping'  id="+i+">Ergebnis</button> </li>"); 
                } else {
                   historyItems.push("<tr><td>"+match.spieldatum+"</td><td>"+match.name_first+" - "+match.name_secound+"</td><td>"+match.score_first+" - "+match.score_secound+"</td></tr>");
                    
                }                      
            });
                MAKINGASTRINGBECAUSEITDOESNTWORKELSE = (historyItems.join(' '));
             
                $('#upcomingGames').append(upcomingItems.join(' '));
                $('#historyGames').append(MAKINGASTRINGBECAUSEITDOESNTWORKELSE);

            }
            
            function buildPlayerSelectForm(player){
            var listitems = [];
            $.each(player, function(i, player)   {
                
                listitems.push("<option value="+i+">"+player[0]+"</option>"); 
            });
            
                
                $('.playerSelectForm').append(listitems.join(' '));

                

            
            // event which created dynamic modal
    
            }

       
            
            
             $("#TipGameModal").on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var matchId = button.data('match');
                var modal = $(this);

                
                $.ajax({
                  url: "ajax/list.ajax.php",
                  type: "get", //send it through get method
                  data:{res: 'matches', bid: matchId},
                  success: function(response) {
                    match =$.parseJSON(response);
                      console.log(match);
                    modal.find('.modal-title').text("Ausgang des Spiels " + match.name_first+" - "+ match.name_secound+" ");
                    modal.find("#tipone").attr( "placeholder", match.name_first+" Tore" );
                    modal.find("#tipone").attr( "mannschaftone", match.name_secound);
                    modal.find("#tiptwo").attr( "placeholder", ""+match.name_secound+" Tore" );
                    modal.find("#tiptwo").attr( "mannschafttwo", ""+match.name_secound);

                      
                      
                      
                      
                      
                  },
                  error: function(xhr) {
                    //Do Something to handle error
                  }
                });
         
              
            });           

            
            
            $("#SetScoreModal").on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var matchId = button.data('match');
                var modal = $(this);

                
                $.ajax({
                  url: "ajax/list.ajax.php",
                  type: "get", //send it through get method
                  data:{res: 'matches', bid: matchId},
                  success: function(response) {
                    match =$.parseJSON(response);
                      console.log(match);
                    modal.find('.modal-title').text("Ausgang des Spiels " + match.name_first+" - "+ match.name_secound+" ");
                    modal.find("#mannschaftone").attr( "placeholder", match.name_first+" Tore" );
                    modal.find("#mannschaftone").attr( "mannschaftone", match.name_secound);
                    modal.find("#mannschafttwo").attr( "placeholder", ""+match.name_secound+" Tore" );
                    modal.find("#mannschafttwo").attr( "mannschafttwo", ""+match.name_secound);

                      
                      
                      
                      
                      
                  },
                  error: function(xhr) {
                    //Do Something to handle error
                  }
                });
         
              
            });
            
            
             init();
        });
        
                    
        
$(function() {

    $('#login-form-link').click(function(e) {
		$("#login-form").delay(100).fadeIn(100);
 		$("#register-form").fadeOut(100);
		$('#register-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#register-form-link').click(function(e) {
		$("#register-form").delay(100).fadeIn(100);
 		$("#login-form").fadeOut(100);
		$('#login-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});

});
        
      
    </script>

  </body>
</html>
