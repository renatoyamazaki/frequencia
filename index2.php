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
			<h2>Cálculo Individual</h2>	
		</div>


		<div class="content">

			<form class="pure-form pure-form-aligned"  name="individual" action="individual.php" method="post">
			<fieldset>

			<div class="pure-control-group">
				<label for="">Matrícula</label>
                                <input name="matricula" type="text" size="8" required>
			</div>

			<div class="pure-control-group">
				<label for="">Data Inicial</label>
				<input name="dt_inicio" type="text" size="8" value="<?php echo date("d/m/Y", time() - 60 * 60 * 24); ?>" id="cal1">
			</div>
			
			<div class="pure-control-group">
				<label for="">Data Final</label>
				<input name="dt_fim" type="text" size="8" value="<?php echo date("d/m/Y"); ?>" id="cal2">
			</div>

			<div class="pure-controls">
				<button type="submit" name="acao" value="calcular" class="pure-button pure-button-primary">Calcular</button>
				<button type="submit" name="acao" value="consultar" class="pure-button pure-button-primary">Consultar</button>
			</div>
			</fieldset>
			</form>
		</div>
	</div>
</div>
</body>
</html>
