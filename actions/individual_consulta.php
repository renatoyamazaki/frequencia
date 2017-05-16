<?php

	/**
		Conexao com a base de dados
	**/
	$db = new conn("sd048cld", "TD01");


	/** 
	
	**/
/*	$sql01 =
$sql01 = 
"SELECT r.escala,
       to_char(r.hr_inicio, 'YYYY-MM-DD HH24:MI:SS') AS hr_inicio,
       to_char(r.hr_fim, 'YYYY-MM-DD HH24:MI:SS') AS hr_fim,
       r.justificativa,
       r.resultado,
       r.efetivado,
       r.periodo
FROM fq_empreg_escala a,
     fq_jornada d,
     fq_resultado r
WHERE a.matricula = :matricula
  AND a.escala = d.escala
  AND r.matricula = a.matricula
  AND r.hr_inicio >= to_date(:dt_inicio, 'DD/MM/YYYY') + d.hr_inicio - interval '6' hour
  AND r.hr_inicio <= to_date(:dt_fim, 'DD/MM/YYYY') + d.hr_inicio + d.duracao + interval '6' hour
ORDER BY 2";
*/
$sql01 = 
"SELECT r.escala,
       to_char(r.hr_inicio, 'YYYY-MM-DD HH24:MI:SS') AS hr_inicio,
       to_char(r.hr_fim, 'YYYY-MM-DD HH24:MI:SS') AS hr_fim,
       r.justificativa,
       r.resultado,
       r.efetivado,
       r.periodo
FROM fq_resultado r
WHERE r.matricula = :matricula
  AND r.hr_inicio >=
    ( SELECT to_date(:dt_inicio, 'dd/mm/yyyy') + d.hr_inicio - interval '6' hour
     FROM fq_empreg_escala a,
          fq_jornada d
     WHERE a.matricula = r.matricula
       AND a.dt_inicio <= to_date(:dt_inicio, 'dd/mm/yyyy')
       AND a.dt_fim > to_date(:dt_inicio, 'dd/mm/yyyy')
       AND a.escala = d.escala )
  AND r.hr_fim <=
    ( SELECT to_date(:dt_fim, 'dd/mm/yyyy') + d.hr_inicio + d.duracao + interval '6' hour
     FROM fq_empreg_escala a,
          fq_jornada d
     WHERE a.matricula = r.matricula
       AND a.dt_inicio <= to_date(:dt_fim, 'dd/mm/yyyy')
       AND a.dt_fim > to_date(:dt_fim, 'dd/mm/yyyy')
       AND a.escala = d.escala )";

	$stmt = oci_parse($db->dbconn, $sql01);		
	oci_bind_by_name($stmt, ":matricula", $matricula);
	oci_bind_by_name($stmt, ":dt_inicio", $dt_inicio);
	oci_bind_by_name($stmt, ":dt_fim", $dt_fim);

	oci_execute($stmt);
	
	$resultados = new resultado();

	while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
		$escala = $row['ESCALA'];
		$dia_inicio = $row['DIA_INICIO'];
		$hr_inicio = $row['HR_INICIO'];
		$hr_fim = $row['HR_FIM'];
		$justificativa = $row['JUSTIFICATIVA'];
		$resultado = $row['RESULTADO'];
		$efetivado = $row['EFETIVADO'];
		$periodo = $row['PERIODO'];

		$interv = $resultados->adicionaIntervalo ($escala, $hr_inicio, $hr_fim, $folga, $periodo);
		if ($interv)
			$interv->insereResultado ($justificativa, $resultado, $efetivado);
	}
?>
