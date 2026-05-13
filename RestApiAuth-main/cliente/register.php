<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGII | Alta de Operadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white text-center py-3">
                        <h4 class="mb-0">SGII - Registro de Personal</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="formularioRegistro">
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Ej: Juan Pérez" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" placeholder="Ej: juan@elsol.com" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Clave de Seguridad</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">Registrar en Sistema</button>
                                <a href="index.php" class="btn btn-outline-secondary">Volver al Acceso</a>
                            </div>
                        </form>
                        
                        <div id="panelAlertasRegistro" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formularioRegistro').addEventListener('submit', async function(evento) {
            evento.preventDefault(); 

            // Extracción de vectores de datos del DOM
            const inputNombre = document.getElementById('nombre').value;
            const inputCorreo = document.getElementById('correo').value;
            const inputPassword = document.getElementById('password').value;
            const panelAlertas = document.getElementById('panelAlertasRegistro');

            // Construcción del Payload estructurado
            const payload = {
                action: 'register',
                nombre: inputNombre,
                correo: inputCorreo,
                password: inputPassword
            };

            try {
                // Petición HTTP POST al microservicio PHP
                const respuestaServidor = await fetch('../servidor/UsuariosAPI.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const datos = await respuestaServidor.json();

                // Lógica de ramificación según respuesta del backend
                if (datos.status === 'success') {
                    panelAlertas.innerHTML = `
                        <div class="alert alert-success" role="alert">
                            <strong>¡Transacción Exitosa!</strong><br>
                            ${datos.message}
                        </div>`;
                    // Limpieza del formulario tras un commit exitoso
                    document.getElementById('formularioRegistro').reset();
                } else {
                    panelAlertas.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <strong>Fallo de Integridad:</strong> ${datos.message}
                        </div>`;
                }

            } catch (errorCritico) {
                console.error('Anomalía en túnel de datos:', errorCritico);
                panelAlertas.innerHTML = `
                    <div class="alert alert-warning" role="alert">
                        Error crítico de latencia o servidor caído. Verifique consola.
                    </div>`;
            }
        });
    </script>
</body>
</html>