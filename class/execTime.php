<?php

class execTime {
	private $executionTime;

    public function __construct () {
		$this->executionTime = microtime(true);
	}

	public function __destruct() {
		echo "<div id='rodape'>Tempo de execução: <b>" . number_format ((microtime(true) - $this->executionTime), 6) . "</b> segundos</div>";
	}

}

?>
