<?php
require_once 'config/conexion.php';

class FormModel extends Conectar {
    public function validateCveDoc($cve_doc) {
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

    public function validatePrec($cve_doc) {
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

    public function updatePrec($cve_doc, $newValue) {
        $conexion = $this->Conexion();
        $query = "UPDATE FACTV04 SET IMPORTE = ? WHERE CVE_DOC = ?";
        $params = array($newValue, $cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $queryP = "UPDATE PAR_FACTV04 SET PREC = ? WHERE NUM_PAR = 1 AND CVE_DOC = ?";
        $paramsP = array($newValue, $cve_doc);
        $stmtP = sqlsrv_query($conexion, $queryP, $paramsP);
        if ($stmtP === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    public function insertObs($campo3,$campo1) {
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
        $paramsUpdateFV = array($newCve,$campo1);
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

    public function insertObsPar($campo2,$campo1) {
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
        $paramsUpdatePFV = array($newCve,$campo1);
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
?>