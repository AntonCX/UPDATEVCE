<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Formulario Actualizar VCE</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="text-center">
            <img src="assets/img/logo.png" alt="Logo" class="img-fluid" style="height: 150px;">
            <h1>Actualizar VCE</h1>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger"> <?php echo $error; ?> </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?php echo $success; ?> </div>
        <?php endif; ?>
                        
        <div id="form-factura" class="mt-5" >
            <form id="registroForm" method="post" action="index.php">
                <div class="form-group">
                    <label for="cve_doc">ID</label>
                    <input type="text" class="form-control" id="cve_doc" name="cve_doc" required>
                </div>
                <div class="form-group">
                    <label for="obs_par">Observaciones de la partida</label>
                    <input type="text" class="form-control" id="obs_par" name="obs_par">
                </div>
                <div class="form-group">
                    <label for="obs_vce">Observaciones VCE</label>
                    <input type="text" class="form-control" id="obs_vce" name="obs_vce">
                </div>
                <div class="form-group">
                    <label for="monto">Monto Consulta</label>
                    <input type="number" class="form-control" id="" name="monto" required> 
                </div>
                <button type="submit" class="btn btn-primary" name="submit">Enviar</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    

</body>
</html>