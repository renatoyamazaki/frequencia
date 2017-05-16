<?php

/**
	Objeto que contera apontamentos para executar as marcacoes
**/
class marcacao {
	
	// Vetor de horarios, contem apontadores de inicio e fim como chave
	public $horariosVetor = array();

	function adicionaIntervalo ($interv, $hr_inicio, $hr_fim) {

		$unix_inicio = strtotime($hr_inicio);
		$unix_fim = strtotime($hr_fim);

		// Vetor com dois indices (um para hora inicial e outro para hora final)
		$this->horariosVetor[$unix_inicio] =  $interv;
		$this->horariosVetor[$unix_fim] = $this->horariosVetor[$unix_inicio];
	}

	function adicionaMarcacao ($dt_marcacao) {
		
		// acha o elemnento no vetor de horarios mais proximo da marcacao
		$proximo = $this->achaProximo($this->horariosVetor, strtotime($dt_marcacao));

		// atualiza a marcacao
		$nova_chave = $this->horariosVetor[$proximo]->atualiza_marcacao($dt_marcacao);

		// atualiza o vetor de horarios
		$this->horariosVetor[$nova_chave] = $this->horariosVetor[$proximo];
		unset($this->horariosVetor[$proximo]);
	}

	// implementar busca binaria se funcionar, quando tiver tempo
	function achaProximo ($arr, $search) {
   		$closest = null;
		foreach ($arr as $key => $value) {
			if ($closest === null || abs($search - $closest) > abs($key - $search)) {
				$closest = $key;
			}
		}
		return $closest;
	}

	function imprime () {
		echo "<pre>";
		print_r ($this);
		echo "</pre>";
	}

}

/**
	Objeto que ira conter as escalas
	Utiliza de um vetor de objetos jornada
**/
class escala {
	
	// Vetor de objetos jornada, nao possui chave definida
	public $jornadaVetor = array ();
	
	function adicionaIntervalo ($escala, $hr_inicio, $hr_fim, $folga, $periodo) {

		// Modifica as jornadas
		if ($periodo == '1') {
			$this->jornadaVetor[] = new jornada ();
		}
		if ((end($this->jornadaVetor))) {
			$interv = end($this->jornadaVetor)->adicionaIntervalo($escala, $hr_inicio, $hr_fim, $periodo);
			$interv->ajustaFolga($folga, $periodo);

			return $interv;
		}		
	}

	function imprime () {
		echo "<pre>";
		print_r ($this);
		echo "</pre>";
	}

}

/**

**/
class resultado extends escala {
	
	function imprime () {

		// impressao colorida na tabela
		$count = 0;
			
		echo "<table class='pure-table'>";
		echo "<thead> <tr> <th colspan=\"3\">intervalo 1</th><th>j1</th><th colspan=\"3\">intervalo 2</th><th>j2</th><th>total</th></tr> </thead>";
		echo "<tbody>";
		foreach ($this->jornadaVetor as $chave => $jornada) {
	
        		if ($count % 2 == 0)
		                echo "<tr>";
		        else
                		echo "<tr class='pure-table-odd'>";
			// resultado (em segundos)
			$total = 0;

			foreach ($jornada->intervaloVetor as $key => $intervalo) {
				$COLOR="";
				switch($intervalo->efetivado) {
					case 2:
						$COLOR="green";
						break;
					case 0:
						$COLOR="red";
						break;
				}
				$total += $intervalo->resultado;
				echo "<td>". date("d/m/Y", $intervalo->hr_inicio) ."</td><td class='$COLOR'>" . date("H:i:s", $intervalo->hr_inicio) . "</td><td class='$COLOR'>" . date("H:i:s", $intervalo->hr_fim) . "</td><td>" . $intervalo->justificativa . "</td>";
			}
			echo "<td>" . convSegIntervalo($total) . "</td>";
			echo "</tr>";

			$count++;
		}
		echo "<tbody>";
		echo "</table>";
	}

}

/**
	Coletanea de 1 ou mais objetos do tipo intervalo
**/
class jornada {

	// Vetor de objetos intervalo, a chave é o periodo
	public $intervaloVetor = array();

	
	function adicionaIntervalo ($escala, $hr_inicio, $hr_fim, $periodo) {

		// Inicia o vetor de intervalos
		$this->intervaloVetor[$periodo] = new intervalo ($escala, $hr_inicio, $hr_fim, $periodo);
	
		// Retorna o objeto intervalo recem adicionado
		return $this->intervaloVetor[$periodo];
	}

	function ajustaFolga ($folga, $periodo) {
		$this->intervaloVetor[$periodo]->ajustaFolga($folga);
	}

	function imprime () {
		echo "<pre>";
		print_r ($this);
		echo "</pre>";
	}

}

/**
	Objeto que contem a estrutura basica de marcacao de ponto
	Armazena informacoes de horarios
**/
class intervalo {
	
	public $escala;
	public $hr_inicio;
	public $hr_fim;
	public $periodo;

	// Efetivado (0:falta,1:ok,2:folga)
	public $efetivado;
	public $resultado;
	public $justificativa;

	function __construct ($escala, $hr_inicio, $hr_fim, $periodo) {
		$this->escala = $escala;
		$this->periodo = $periodo;
		$this->hr_inicio = strtotime($hr_inicio);
		$this->hr_fim = strtotime($hr_fim);
	}

	function ajustaFolga ($folga) {
		if ($folga == '0') {
			$this->efetivado = '0';
			$this->resultado = $this->hr_inicio - $this->hr_fim;
		}
		else {
			$this->efetivado = '2';	
			$this->resultado = $this->hr_fim - $this->hr_fim;
		}
	}

	function insereResultado($justificativa, $resultado, $efetivado) {
		$this->justificativa = $justificativa;
		$this->resultado = $resultado;
		$this->efetivado = $efetivado;
	}
	
	// hr_marcacao eh o novo horario
	function atualiza_marcacao ($hr_marcacao) {

		$hr_marcacao = strtotime($hr_marcacao);
		
		$interval1 = abs($this->hr_inicio - $hr_marcacao);
		$interval2 = abs($this->hr_fim - $hr_marcacao);

		// Verifica se a atualizacao do horario sera no inicio ou fim
		if ( $interval1 < $interval2 ) {			
			// Se não existir resultado ja calculado
			if ($this->efetivado == '0')
				$this->resultado = $this->hr_inicio - $hr_marcacao;
			// Soma o resultado anterior com o atual
			else
				$this->resultado = $this->resultado + $this->hr_inicio - $hr_marcacao;
			$this->hr_inicio = $hr_marcacao;
		}
		else {		
			// Se não existir resultado ja calculado
			if ($this->efetivado == '0')
				$this->resultado = $hr_marcacao - $this->hr_fim;
			// Soma o resultado anterior com o atual
			else
				$this->resultado = $this->resultado + $hr_marcacao - $this->hr_fim;
			$this->hr_fim = $hr_marcacao;
		}
		$this->efetivado = 1;

		// Retorna o unx time da marcacao, sera atualizado o vetor de horarios
		return ($hr_marcacao);
	}

	function imprime () {
		echo "<pre>";
		print_r ($this);
		echo "</pre>";
	}

}

function convSegIntervalo ($segundos) {
	$intervalo = gmdate("H:i:s", abs($segundos));
	if ($segundos < 0)
		$intervalo = '-' . $intervalo;
	else	
		$intervalo = '+' . $intervalo;
	return $intervalo;
}

function convert($size)
{
    $unit=array('B','KB','MB','GB','TB','PB');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

?>
