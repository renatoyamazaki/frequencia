<?php

	require_once "class/execTime.php";
	require_once "class/connection.php";
	require_once "class/frequencia.php";

	$e = new execTime();
	
	/**
		Variaveis retornadas pelo formulario
	**/
	$matricula = $_POST['matricula'];
	$dt_inicio = $_POST['dt_inicio'];
	$dt_fim = $_POST['dt_fim'];	
 	$acao =	$_REQUEST['acao'];

	if ($acao == "calcular")
		require_once "actions/individual_calculo.php";
	else {
		if ($acao == "consultar")
			require_once "actions/individual_consulta.php";
	}
	
?>
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
		
<?php
	if ($acao == "calcular") {

		echo "<h2 class=\"content-subhead\">Cálculo | Matrícula $matricula | Intervalo '$dt_inicio' e '$dt_fim'</h2>";

		echo "Cálculo individual concluído.";
		echo "Memória utilizada: " . convert(memory_get_usage(true));

?>
			<form class="pure-form pure-form-aligned"  name="individual" action="individual.php" method="post">
			<fieldset>
			
			<input type="hidden" name="matricula" value="<?php echo $matricula;  ?>">
			<input type="hidden" name="dt_inicio" value="<?php echo $dt_inicio; ?>">
			<input type="hidden" name="dt_fim" value="<?php echo $dt_fim; ?>">

			<div class="pure-controls">
				<button type="submit" name="acao" value="consultar" class="pure-button pure-button-primary">Resultado</button>
			</div>
			</fieldset>
			</form>
<?php
	} else {
		if ($acao == "consultar") {
			
			echo "<h2 class=\"content-subhead\">Consulta | Matrícula $matricula | Intervalo '$dt_inicio' e '$dt_fim'</h2>";
			$resultados->imprime();

			echo "Memória utilizada: " . convert(memory_get_usage(true));
		}
	}
	
?>
		</div>
	</div>
</div>
</body>
</html>
