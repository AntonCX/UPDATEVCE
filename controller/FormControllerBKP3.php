<?php
require_once 'models/FormModel.php';

class FormController {
    public function index() {
        require_once 'view/formFV.php';
    }

    public function submit() {
        $formModel = new FormModel();
        $campo1ini = $_POST['cve_doc'];
        $campo2 = $_POST['obs_par'];
        $campo3 = $_POST['obs_vce'];
        $campo4 = $_POST['monto'];

	//Validacion que el monto sea un numero decimal valido
	if(!filter_var($campo4,FILTER_VALIDATE_FLOAT)){
		$error = 'El VCE es incorrecto';
            	require_once 'view/formFV.php';
            	return;	
	}

	//Transformar cve_doc
	$campo1 = 'VCE' . str_repeat(' ',10 - strlen($campo1ini)) . $campo1ini;

        // Validaciones
        if (!$formModel->validateCveDoc($campo1)) {
            $error = 'El VCE es incorrecto';
            require_once 'view/formFV.php';
            return;
        }

        if (!$formModel->validatePrec($campo1)) {
            $error = 'El VCE ya tiene cargo';
            require_once 'view/formFV.php';
            return;
        }

        $formModel->updatePrec($campo1, $campo4);
//================================================================
  public function insetCuenM($CVE_CLPV , $newValue, $cve_doc) {
        $conexion = $this->Conexion();
        $query = "INSERT INTO CUEN_M04(CVE_CLIE, REFER, NUM_CPTO, NUM_CARGO, CVE_OBS, NO_FACTURA, DOCTO, IMPORTE, FECHA_APLI, 
	FECHA_VENC, AFEC_COI, STRCVEVEND, NUM_MONED, TCAMBIO, IMPMON_EXT, FECHAELAB, CTLPOL, CVE_FOLIO, TIPO_MOV, 
	CVE_BITA, SIGNO, CVE_AUT, USUARIO, ENTREGADA, FECHA_ENTREGA, STATUS, REF_SIST, UUID, VERSION_SINC, USUARIOGL)
	
	SELECT CVE_CLPV, ?, 2, 1, 0, ?, ?, GETDATE(), GETDATE(), 'A', ' ', 1, 1, ?, GETDATE(), NULL, NULL, 'C', NULL, 1, NULL, 
	550, NULL, NULL, 'A', NULL, NEWID(), GETDATE(), 135 FROM FACTV04";
        $params = array($CVE_CLPV, $newValue, $cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

public function updateCUEN_M04($cve_doc, $newValue) {
        $conexion = $this->Conexion();
        $query = "UPDATE CUEN_M04 SET IMPORTE= ? WHERE REFER = ?";
        $params = array($newValue, $cve_doc);
        $stmt = sqlsrv_query($conexion, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }		
        $queryP = "UPDATE CUEN_M04 SET IMPMON_EXT= ? WHERE REFER = ?";
        $paramsP = array($newValue, $cve_doc);
        $stmtP = sqlsrv_query($conexion, $queryP, $paramsP);
        if ($stmtP === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

//===================================================================

        if (!empty($campo2)) {
            $formModel->insertObsPar($campo2,$campo1);
        }

        if (!empty($campo3)) {
            $formModel->insertObs($campo3,$campo1);
        }

        $success = 'Datos procesados correctamente';
        require_once 'view/formFV.php';
    }
}
?>
