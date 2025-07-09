<?php
class BackupController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function generarBackup() {
        try {
            // Verificar si mysqldump está disponible en la instalación de XAMPP
            $mysqldumpPath = 'C:\xampp\mysql\bin\mysqldump.exe';
            if (!file_exists($mysqldumpPath)) {
                throw new Exception('mysqldump no está disponible en la ruta esperada de XAMPP (C:\xampp\mysql\bin\mysqldump.exe). Por favor, verifica que MySQL está instalado correctamente en XAMPP.');
            }

            $dbConfig = new Database();
            $dbName = 'licoreria';
            $backupDir = dirname(__DIR__) . '/backups/';
            
            // Verificar permisos de escritura
            if (!is_writable(dirname(__DIR__))) {
                throw new Exception('No hay permisos de escritura en el directorio del proyecto');
            }

            // Crear directorio de backups si no existe
            if (!file_exists($backupDir)) {
                if (!mkdir($backupDir, 0777, true)) {
                    throw new Exception('No se pudo crear el directorio de backups');
                }
            }

            if (!is_writable($backupDir)) {
                throw new Exception('No hay permisos de escritura en el directorio de backups');
            }

            // Generar nombre único para el archivo de backup
            $fileName = 'backup_' . date('H-d-m-Y') . '.sql';
            $filePath = $backupDir . $fileName;

            // Comando para generar el backup usando la ruta completa de mysqldump
            $command = sprintf(
                '"%s" --host=%s --user=%s --password=%s --databases %s > %s 2>&1',
                $mysqldumpPath,
                'localhost',
                'root',
                '',
                $dbName,
                $filePath
            );

            // Ejecutar el comando y capturar la salida de error
            exec($command, $output, $returnVar);
            
            // Verificar si el archivo se creó y tiene contenido
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                throw new Exception('El archivo de backup no se creó correctamente');
            }

            if ($returnVar === 0) {
                // Forzar la descarga del archivo
                if (file_exists($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    if ($fileContent !== false) {
                        // Codificar el contenido en base64
                        $base64Content = base64_encode($fileContent);
                        
                        // Eliminar el archivo físico
                        unlink($filePath);
                        
                        // Enviar respuesta JSON con el contenido del archivo
                        header('Content-Type: application/json');
                        echo json_encode([
                            'message' => 'Backup generado exitosamente',
                            'status' => 'success',
                            'filename' => basename($filePath),
                            'content' => $base64Content
                        ]);
                        exit;
                    } else {
                        throw new Exception('Error al leer el archivo de backup');
                    }
                }
            } else {
                throw new Exception('Error al generar el backup. Código de error: ' . $returnVar);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (!empty($output)) {
                $errorMessage .= "\nDetalles adicionales: " . implode("\n", $output);
            }
            header('Content-Type: application/json');
            echo json_encode([
                'error' => $errorMessage,
                'status' => 'error'
            ]);
        }
    }

    private function verificarPermisos($directorio) {
        if (!file_exists($directorio)) {
            return false;
        }
        $tempFile = $directorio . '/test_' . uniqid() . '.tmp';
        $handle = @fopen($tempFile, 'w');
        if ($handle) {
            fclose($handle);
            unlink($tempFile);
            return true;
        }
        return false;
    }
}