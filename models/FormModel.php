<?php
require_once 'config/conexion.php';

class FormModel extends Conectar
{
    public function validateCveDoc($cve_doc)
    {
        $conexion = $this->Conexion();
        $query = "SELECT COUNT(*) AS count FROM FACTV04 WHERE CVE_DOC = ?";
        $params = array($cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        return $row['count'] > 0;
    }

    public function validatePrec($cve_doc)
    {
        $conexion = $this->Conexion();
        $query = "SELECT IMPORTE FROM FACTV04 WHERE CVE_DOC = ?";
        $params = array($cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        return $row['IMPORTE'] <= 0.01;
    }

    public function updatePrec($cve_doc, $newValue, $impu)
    {
        $conexion = $this->Conexion();
        /****************Primero se actualiza la partida********************/
        //Se actualiza el monto y SOLO el valor del impuesto
        $queryP = "UPDATE PAR_FACTV04 SET PREC = ?,TOT_PARTIDA =?, IMPU4 = ? WHERE NUM_PAR = 1 AND CVE_DOC = ?";
        $paramsP = array($newValue, $newValue, $impu,$cve_doc);
        $stmtP = sqlsrv_query($conexion, $queryP, $paramsP);
        if ($stmtP === false) {
            die(print_r(sqlsrv_errors(), true));
        }
	//Se realiza la transaccion para calcular el TOTIMP4
	sqlsrv_begin_transaction($conexion);
	try{
		$queryTr = "
 			UPDATE PAR_FACTV04 SET 
    			TOTIMP4 = CANT * PREC *
       				(
	   			CASE
					WHEN IMPU4 = 0 THEN 100
     					ELSE IMPU4
	   			END
				) / 100
	   		WHERE NUM_PAR = 1 AND CVE_DOC = ?;
  		";
		$paramsTr = [
			$cve_doc
		];
		$stmtTr = sqlsrv_query($conexion,$queryTr, $paramsTr);
		if ($stmtTr === false) {
            	die(print_r(sqlsrv_errors(), true));
        	}
		sqlsrv_commit($conexion);
	}
	catch(Exception $e){
		sqlsrv_rollback($conexion);
		die("Error en la transaccion: " .$e->getMessage);
	}
	/**************Se actualiza el documento********************/
        //===================================================================================================================
        $query = "UPDATE FACTV04 SET IMPORTE = ?, CAN_TOT = ? WHERE CVE_DOC = ?";
        $params = array($newValue, $newValue, $cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
	//Transaccion para actualizar la suma de los impuestos de todas la partidas del documento
	sqlsrv_begin_transaction($conexion);
	try{
		$queryTr2 = "
 			UPDATE FACTV04 SET 
    			IMP_TOT4 = (SELECT SUM(IMPU4) FROM PAR_FACTV04 WHERE CVE_DOC = ?)
	   		WHERE CVE_DOC = ?;
      			UPDATE FACTV04 SET 
    			IMPORTE = CAN_TOT + IMP_TOT4
	   		WHERE CVE_DOC = ?;
  		";
		$paramsTr2 = [
			$cve_doc
		];
		$stmtTr2 = sqlsrv_query($conexion,$queryTr2, $paramsTr2);
		if ($stmtTr2 === false) {
            	die(print_r(sqlsrv_errors(), true));
        	}
		sqlsrv_commit($conexion);
	}
	catch(Exception $e){
		sqlsrv_rollback($conexion);
		die("Error en la transaccion: " .$e->getMessage);
	}
	
    }

    public function insertCuenM($cve_doc, $campo4)
    {
        $conexion = $this->Conexion();

        // Buscar el valor del campo CVE_CLPV en la tabla FACTV04
        $query = "SELECT CVE_CLPV FROM FACTV04 WHERE CVE_DOC = ?";
        $params = array($cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $cve_clpv = $row['CVE_CLPV'];

        // Verificar si existe el registro en la tabla CUEN_M04
        $queryCheck = "SELECT COUNT(*) AS count FROM CUEN_M04 WHERE REFER = ?";
        $paramsCheck = array($cve_doc);
        $stmtCheck = sqlsrv_query($conexion, $queryCheck, $paramsCheck);
        if ($stmtCheck === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

        if ($rowCheck['count'] > 0) {
            // Realizar un update si existe el registro
            $queryUpdate = "UPDATE CUEN_M04 SET IMPORTE
			= ? , IMPMON_EXT = ? WHERE REFER = ?";
            $paramsUpdate = array($campo4, $campo4, $cve_doc);
            $stmtUpdate = sqlsrv_query($conexion, $queryUpdate, $paramsUpdate);
            if ($stmtUpdate === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } else {
            // Realizar un insert si no existe el registro
            $queryInsert = "INSERT INTO CUEN_M04 (CVE_CLIE, REFER, NUM_CPTO, NUM_CARGO, CVE_OBS, NO_FACTURA, DOCTO, IMPORTE,
                FECHA_APLI, FECHA_VENC, AFEC_COI, STRCVEVEND, NUM_MONED, TCAMBIO, IMPMON_EXT, FECHAELAB, TIPO_MOV, SIGNO, USUARIO,
                STATUS, UUID, VERSION_SINC, USUARIOGL) 
                VALUES (?, ?, 2, 1, 0, ?, ?, ?, GETDATE(), GETDATE(), 'A', ' ', 1, 1, ?, GETDATE(), 'C', 1, 544, 'A', NEWID(), GETDATE(), 79)";
            $paramsInsert = array($cve_clpv, $cve_doc, $cve_doc, $cve_doc, $campo4, $campo4);
            $stmtInsert = sqlsrv_query($conexion, $queryInsert, $paramsInsert);
            if ($stmtInsert === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }

    public function insertObs($campo3, $campo1)
    {
        $conexion = $this->Conexion();
        $query = "SELECT ULT_CVE FROM TBLCONTROL04 WHERE ID_TABLA = 56";
        $stmt = sqlsrv_query($conexion, $query);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $newCve = $row['ULT_CVE'] + 1;

        $insertQuery = "INSERT INTO OBS_DOCF04 (CVE_OBS, STR_OBS) VALUES (?, ?)";
        $paramsInsert = array($newCve, $campo3);
        $stmtInsert = sqlsrv_query($conexion, $insertQuery, $paramsInsert);
        if ($stmtInsert === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $updateQueryFV = "UPDATE FACTV04 SET CVE_OBS = ? WHERE CVE_DOC = ?";
        $paramsUpdateFV = array($newCve, $campo1);
        $stmtUpdateFV = sqlsrv_query($conexion, $updateQueryFV, $paramsUpdateFV);
        if ($stmtUpdateFV === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $updateQuery = "UPDATE TBLCONTROL04 SET ULT_CVE = ? WHERE ID_TABLA = 56";
        $paramsUpdate = array($newCve);
        $stmtUpdate = sqlsrv_query($conexion, $updateQuery, $paramsUpdate);
        if ($stmtUpdate === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    public function insertObsPar($campo2, $campo1)
    {
        $conexion = $this->Conexion();
        $query = "SELECT ULT_CVE FROM TBLCONTROL04 WHERE ID_TABLA = 56";
        $stmt = sqlsrv_query($conexion, $query);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $newCve = $row['ULT_CVE'] + 1;

        $insertQuery = "INSERT INTO OBS_DOCF04 (CVE_OBS, STR_OBS) VALUES (?, ?)";
        $paramsInsert = array($newCve, $campo2);
        $stmtInsert = sqlsrv_query($conexion, $insertQuery, $paramsInsert);
        if ($stmtInsert === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $updateQueryPFV = "UPDATE PAR_FACTV04 SET CVE_OBS = ? WHERE CVE_DOC = ?";
        $paramsUpdatePFV = array($newCve, $campo1);
        $stmtUpdatePFV = sqlsrv_query($conexion, $updateQueryPFV, $paramsUpdatePFV);
        if ($stmtUpdatePFV === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $updateQuery = "UPDATE TBLCONTROL04 SET ULT_CVE = ? WHERE ID_TABLA = 56";
        $paramsUpdate = array($newCve);
        $stmtUpdate = sqlsrv_query($conexion, $updateQuery, $paramsUpdate);
        if ($stmtUpdate === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}
