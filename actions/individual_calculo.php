<?php
	/**
		Conexao com a base de dados
	**/
	$db = new conn("sd048cld", "TD01");


	/** 
		Coloca em um vetor de escalas todas as previsoes de registros
	**/
$sql01 =
"SELECT c.folga,
       a.escala,
       to_char(c.dt + b.hr_inicio, 'YYYY-MM-DD HH24:MI:SS') AS hr_inicio,
       to_char(c.dt + b.hr_inicio + b.duracao, 'YYYY-MM-DD HH24:MI:SS') AS hr_fim,
       periodo
FROM fq_empreg_escala a,
     fq_escala b,
     fq_calendario c,
     fq_jornada d
WHERE a.matricula = :matricula
  AND a.escala = b.escala
  AND a.dt_inicio <= c.dt
  AND a.dt_fim > c.dt
  AND c.escala = a.escala
  AND c.escala = d.escala
  AND c.dt + b.hr_inicio >= to_date(:dt_inicio, 'DD/MM/YYYY') + d.hr_inicio
  AND c.dt + b.hr_inicio + b.duracao <= to_date (:dt_fim, 'DD/MM/YYYY') + d.hr_inicio + d.duracao
ORDER BY 3";

	$stmt = oci_parse($db->dbconn, $sql01);		
	oci_bind_by_name($stmt, ":matricula", $matricula);
	oci_bind_by_name($stmt, ":dt_inicio", $dt_inicio);
	oci_bind_by_name($stmt, ":dt_fim", $dt_fim);

	oci_execute($stmt);
	
	$escalas = new escala();
	$marcacao = new marcacao();

	while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
		$escala = $row['ESCALA'];
		$hr_inicio = $row['HR_INICIO'];
		$hr_fim = $row['HR_FIM'];
		$folga = $row['FOLGA'];
		$periodo = $row['PERIODO'];

		$interv = $escalas->adicionaIntervalo($escala, $hr_inicio, $hr_fim, $folga, $periodo);
		if ($interv)
			$marcacao->adicionaIntervalo($interv, $hr_inicio, $hr_fim);
	}


	/**
		Retorna os registros de marcacao de ponto do empregado
	**/
/*	$sql02 =
"SELECT to_char(h.he22_dt_registro, 'YYYY-MM-DD HH24:MI:SS') AS dt_marcacao
FROM fq_empreg_escala a,
     fq_jornada d,
     he22 h
WHERE a.matricula = :matricula
  AND a.escala = d.escala
  AND ltrim(h.he22_st_matricula, '0') = a.matricula
  AND h.he22_dt_registro >= to_date(:dt_inicio, 'DD/MM/YYYY') + d.hr_inicio - interval '6' hour
  AND h.he22_dt_registro <= to_date(:dt_fim, 'DD/MM/YYYY') + d.hr_inicio + d.duracao + interval '6' hour";
*/
$sql02 =
"SELECT to_char(h.he22_dt_registro, 'YYYY-MM-DD HH24:MI:SS') AS dt_marcacao
FROM he22 h
WHERE ltrim(h.he22_st_matricula, '0') = :matricula
  AND h.he22_dt_registro >=
    (SELECT to_date(:dt_inicio, 'dd/mm/yyyy') + d.hr_inicio - interval '6' hour
     FROM fq_empreg_escala a,
          fq_jornada d
     WHERE a.matricula = :matricula
       AND a.dt_inicio <= to_date(:dt_inicio, 'dd/mm/yyyy')
       AND a.dt_fim > to_date(:dt_inicio, 'dd/mm/yyyy')
       AND a.escala = d.escala )
  AND h.he22_dt_registro <=
    (SELECT to_date(:dt_fim, 'dd/mm/yyyy') + d.hr_inicio + d.duracao + interval '6' hour
     FROM fq_empreg_escala a,
          fq_jornada d
     WHERE a.matricula = :matricula
       AND a.dt_inicio <= to_date(:dt_fim, 'dd/mm/yyyy')
       AND a.dt_fim > to_date(:dt_fim, 'dd/mm/yyyy')
       AND a.escala = d.escala )";

	$stmt = oci_parse($db->dbconn, $sql02);
	oci_bind_by_name($stmt, ":matricula", $matricula);
	oci_bind_by_name($stmt, ":dt_inicio", $dt_inicio);
	oci_bind_by_name($stmt, ":dt_fim", $dt_fim);

	oci_execute($stmt);
	
	while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
		$dt_marcacao = $row['DT_MARCACAO'];

		$marcacao->adicionaMarcacao($dt_marcacao);
	}

	// Seta o formato de data no oracle
	$stmt = oci_parse($db->dbconn, " alter session set nls_date_format='dd/mm/yyyy hh24:mi:ss'");
        oci_execute($stmt, OCI_DEFAULT);	


	/**
		Remove os registros já existentes na tabela de resultados
	 **/
$sql03 =
"DELETE
FROM fq_resultado r2
WHERE (r2.matricula,
       r2.hr_inicio,
       r2.hr_fim) IN
    (SELECT r.matricula,
            r.hr_inicio,
            r.hr_fim
     FROM fq_empreg_escala a,
          fq_jornada d,
          fq_resultado r
     WHERE a.matricula = :matricula
       AND a.escala = d.escala
       AND r.matricula = a.matricula
       AND r.hr_inicio >= to_date(:dt_inicio, 'DD/MM/YYYY') + d.hr_inicio - interval '6' hour
       AND r.hr_inicio <= to_date(:dt_fim, 'DD/MM/YYYY') + d.hr_inicio + d.duracao + interval '6' hour )";

	$stmt = oci_parse($db->dbconn, $sql03);
	oci_bind_by_name($stmt, ":matricula", $matricula);
	oci_bind_by_name($stmt, ":dt_inicio", $dt_inicio);
	oci_bind_by_name($stmt, ":dt_fim", $dt_fim);

	oci_execute($stmt);


	/**
		Insere na tabela de resultados todos os calculos concluidos
	**/
	$sql03 =
"insert into fq_resultado (MATRICULA, ESCALA, HR_INICIO, HR_FIM, JUSTIFICATIVA, RESULTADO, EFETIVADO, PERIODO)
values (:matricula, :escala, :hr_inicio, :hr_fim, '', :resultado, :efetivado, :periodo)";

	$stmt = oci_parse($db->dbconn, $sql03);
	
	foreach ($escalas->jornadaVetor as $jornada) {

		foreach ($jornada->intervaloVetor as $chave => $intervalo) {

			oci_bind_by_name($stmt, ":matricula", $matricula);
			oci_bind_by_name($stmt, ":escala", $intervalo->escala);
			oci_bind_by_name($stmt, ":hr_inicio", date("d/m/Y H:i:s", $intervalo->hr_inicio));
			oci_bind_by_name($stmt, ":hr_fim", date("d/m/Y H:i:s", $intervalo->hr_fim)); 
			oci_bind_by_name($stmt, ":resultado", $intervalo->resultado);
			oci_bind_by_name($stmt, ":efetivado", $intervalo->efetivado);
			oci_bind_by_name($stmt, ":periodo", $intervalo->periodo);

			oci_execute($stmt);
		}
	}
	
	// COMMIT
        $stmt = oci_parse($db->dbconn, "commit");
        oci_execute($stmt, OCI_DEFAULT);
/**
**/
?>
