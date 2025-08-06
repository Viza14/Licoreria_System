<!DOCTYPE html>
<html>
<head>
    <title>Prueba de Búsqueda de Transacciones</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, button { padding: 8px; margin: 5px 0; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Prueba de Búsqueda de Transacciones</h1>
        
        <div class="form-group">
            <label for="numero_transaccion">Número de Transacción:</label>
            <input type="text" id="numero_transaccion" placeholder="Ej: TXN-ENTRADA-OTRO-001">
            <button onclick="buscarTransaccion()">Buscar</button>
        </div>
        
        <div class="form-group">
            <h3>Transacciones de prueba disponibles:</h3>
            <button onclick="buscarTransaccion('TXN-ENTRADA-OTRO-001')">TXN-ENTRADA-OTRO-001 (ENTRADA_OTRO)</button><br>
            <button onclick="buscarTransaccion('TXN-SALIDA-OTRO-001')">TXN-SALIDA-OTRO-001 (SALIDA_OTRO)</button><br>
            <button onclick="buscarTransaccion('TXN-PERDIDA-001')">TXN-PERDIDA-001 (SALIDA_PERDIDA)</button><br>
        </div>
        
        <div id="resultado" class="result" style="display: none;"></div>
    </div>

    <script>
        function buscarTransaccion(numero = null) {
            const numeroTransaccion = numero || document.getElementById('numero_transaccion').value.trim();
            
            if (!numeroTransaccion) {
                alert('Por favor ingrese un número de transacción');
                return;
            }
            
            document.getElementById('resultado').innerHTML = 'Buscando...';
            document.getElementById('resultado').style.display = 'block';
            
            $.ajax({
                url: 'index.php?action=movimientos-inventario&method=buscarTransaccion',
                method: 'POST',
                data: {
                    numero_transaccion: numeroTransaccion
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta completa:', response);
                    
                    let html = '<h3>Resultado de la búsqueda:</h3>';
                    html += '<pre>' + JSON.stringify(response, null, 2) + '</pre>';
                    
                    if (response.success) {
                        html += '<div class="success">✓ Búsqueda exitosa</div>';
                        
                        if (response.es_perdida) {
                            html += '<div class="success">→ Debería redirigir a modificarPerdida</div>';
                            html += '<div>URL: ' + response.redirect_url + '</div>';
                        } else if (response.es_venta) {
                            html += '<div class="success">→ Debería redirigir a modificarVenta</div>';
                            html += '<div>URL: ' + response.redirect_url + '</div>';
                        } else if (response.es_otro_entrada) {
                            html += '<div class="success">→ Debería redirigir a modificarOtroEntrada</div>';
                            html += '<div>URL: ' + response.redirect_url + '</div>';
                        } else if (response.es_otro_salida) {
                            html += '<div class="success">→ Debería redirigir a modificarOtroSalida</div>';
                            html += '<div>URL: ' + response.redirect_url + '</div>';
                        } else {
                            html += '<div class="success">→ Debería mostrar formulario de ajuste</div>';
                        }
                    } else {
                        html += '<div class="error">✗ Error: ' + response.message + '</div>';
                    }
                    
                    document.getElementById('resultado').innerHTML = html;
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', xhr.responseText);
                    document.getElementById('resultado').innerHTML = 
                        '<div class="error">Error AJAX: ' + error + '</div>' +
                        '<div>Respuesta del servidor: ' + xhr.responseText + '</div>';
                }
            });
        }
    </script>
</body>
</html>