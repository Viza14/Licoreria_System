<?php
function obtenerNotificacionesStock() {
    require_once ROOT_PATH . 'models/StockLimiteModel.php';
    $stockModel = new StockLimiteModel();
    
    $stockBajo = $stockModel->obtenerProductosConStockBajo();
    $stockAlto = $stockModel->obtenerProductosConStockAlto();
    
    $notificaciones = [];
    $total = count($stockBajo) + count($stockAlto);
    
    if (!empty($stockBajo)) {
        $notificaciones[] = [
            'tipo' => 'danger',
            'icono' => 'bolt',
            'mensaje' => 'Stock bajo en '.count($stockBajo).' producto(s)',
            'url' => BASE_URL.'index.php?action=gestion-stock&method=alertas',
            'tiempo' => 'Reciente'
        ];
    }
    
    if (!empty($stockAlto)) {
        $notificaciones[] = [
            'tipo' => 'warning',
            'icono' => 'exclamation-triangle',
            'mensaje' => 'Stock alto en '.count($stockAlto).' producto(s)',
            'url' => BASE_URL.'index.php?action=gestion-stock&method=alertas',
            'tiempo' => 'Reciente'
        ];
    }
    
    return [
        'total' => $total,
        'notificaciones' => $notificaciones,
        'hayAlertas' => $total > 0
    ];
}