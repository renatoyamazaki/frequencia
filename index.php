<!doctype html>
<html>

<head>
<title>Frequência</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- CSS -->
<link rel="stylesheet" href="css/jquery.datepick.css" />
<link rel="stylesheet" href="css/pure-min.css" />
<link rel="stylesheet" href="css/side-menu.css" />
<link rel="stylesheet" href="css/freq.css" />


<!-- JS -->
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/jquery.auto-complete.min.js"></script>
<script src="js/jquery.plugin.min.js"></script>
<script src="js/sorttable.js"></script>
<script src="js/jquery.datepick.min.js"></script>
<script src="js/jquery.datepick-pt-BR.js"></script>


<script>
$(function() {	
	// campos de calendario
	$('#cal1').datepick();
});

$(function() {	
	// campos de calendario
	$('#cal2').datepick();
});
</script>

</head>


<body>

<div id ="layout">

       <!-- Menu toggle -->
      	<a href="#menu" id="menuLink" class="menu-link"><span></span></a>

        <div id="menu">
       	        <div class="pure-menu">
               	        <a class="pure-menu-heading" href="index.php"> Index </a>
              	 	<ul class="pure-menu-list">
                               	<li class="pure-menu-item"><a href="index2.php" class="pure-menu-link">Individual</a></li>
                        	<li class="pure-menu-item"><a href="index.php" class="pure-menu-link">Por Escala</a></li>
                        	<li class="pure-menu-item"><a href="index.php" class="pure-menu-link">Coordenadoria</a></li>
                        	<li class="pure-menu-item"><a href="index.php" class="pure-menu-link">Completo</a></li>

                       	</ul>
               	</div>
      	</div>

	<div id="main">

		<div class="header">	
			<h1>Frequência</h1>
		</div>


		<div class="content">

		</div>
	</div>
</div>
</body>
</html>
