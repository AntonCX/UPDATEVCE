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

    // Primera actualización en FACTV04
    $query = "UPDATE FACTV04 SET IMPORTE = ? WHERE CVE_DOC = ?";
    $params = array($newValue, $cve_doc);
    $stmt = sqlsrv_query($conexion, $query, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Actualización en PAR_FACTV04
    $queryP = "UPDATE PAR_FACTV04 SET PREC = ? WHERE NUM_PAR = 1 AND CVE_DOC = ?";
    $paramsP = array($newValue, $cve_doc);
    $stmtP = sqlsrv_query($conexion, $queryP, $paramsP);
    if ($stmtP === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Inserción en CUEN_M04
    $queryC = "INSERT INTO CUEN_M04(CVE_CLIE, REFER, NUM_CPTO, NUM_CARGO, CVE_OBS, NO_FACTURA, DOCTO, IMPORTE, FECHA_APLI, 
    FECHA_VENC, AFEC_COI, STRCVEVEND, NUM_MONED, TCAMBIO, IMPMON_EXT, FECHAELAB, CTLPOL, CVE_FOLIO, TIPO_MOV, 
    CVE_BITA, SIGNO, CVE_AUT, USUARIO, ENTREGADA, FECHA_ENTREGA, STATUS, REF_SIST, UUID, VERSION_SINC, USUARIOGL)
    
    SELECT CVE_CLPV, ?, 2, 1, 0, ?, ?, GETDATE(), GETDATE(), 'A', ' ', 1, 1, ?, GETDATE(), NULL, NULL, 'C', NULL, 1, NULL, 
    550, NULL, NULL, 'A', NULL, NEWID(), GETDATE(), 135 FROM FACTV04";

    $paramsC = array( $newValue,$newValue,$newValue,$newValue, $cve_doc);
    $stmtC = sqlsrv_query($conexion, $queryC, $paramsC);
    if ($stmtC === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Actualización en CUEN_M04 con queryN
    $queryN = "UPDATE CUEN_M04 SET IMPORTE = ? WHERE REFER = ?";
    $paramsN = array($newValue, $cve_doc);
    $stmtN = sqlsrv_query($conexion, $queryN, $paramsN);
    if ($stmtN === false) {  // Aquí debería ser $stmtN en lugar de $stmt
        die(print_r(sqlsrv_errors(), true));
    }

    // Actualización en CUEN_M04 con queryM
    $queryM = "UPDATE CUEN_M04 SET IMPMON_EXT = ? WHERE REFER = ?";
    $paramsM = array($newValue, $cve_doc);
    $stmtM = sqlsrv_query($conexion, $queryM, $paramsM);
    if ($stmtM === false) {  // Aquí debería ser $stmtM en lugar de $stmt
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