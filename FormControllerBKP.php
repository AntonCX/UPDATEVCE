<?php
require_once 'models/FormModel.php';

class FormController {
    public function index() {
        require_once 'view/formFV.php';
    }

    public function submit() {
        $formModel = new FormModel();
        $campo1 = $_POST['cve_doc'];
        $campo2 = $_POST['obs_par'];
        $campo3 = $_POST['obs_vce'];
        $campo4 = $_POST['monto'];

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


