<?php
// clase para manejar el formulario y los datos del usuario
class RegistroFormulario {
    private $nombre;
    private $carnet;
    private $correo;
    private $edad;

    // este es el constructor, aqui se inicializan los datos
    public function __construct($nombre, $carnet, $correo, $edad) {
        $this->nombre = $nombre;
        $this->carnet = $carnet;
        $this->correo = $correo;
        $this->edad = $edad;
    }

   // esta funcion valida todos los campos usando expresiones regulares 
    public function validar() {
        $errores = [];

        // validando el nombre con letras y espacios, incluye acentos y la ñ
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $this->nombre)) {
            $errores[] = "El nombre solo debe contener letras y espacios.";
        }

        // validando carnet, debe tener 2 letras mayusculas y 6 numeros
        if (!preg_match("/^[A-Z]{2}[0-9]{6}$/", $this->carnet)) {
            $errores[] = "Número de carnet inválido. Debe tener el formato estándar (ej: AB123456).";
        }

        // aqui se valida el correo usando un patron basico
        if (!preg_match("/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/", $this->correo)) {
            $errores[] = "Correo electrónico inválido.";
        }
        // la edad debe ser numero y estar entre 18 y 99
        if (!preg_match("/^\d+$/", $this->edad) || $this->edad < 18 || $this->edad > 99) {
            $errores[] = "Edad inválida. Debe estar entre 18 y 99.";
        }

        return $errores; // retorna los errores si hay
    }

// esta funcion guarda los datos en el archivo csv
    public function guardarEnArchivo() {
        $archivo = fopen("registros.csv", "a"); // abriendo archivo en modo añadir
        if ($archivo) {
            // escribiendo los datos como una linea en formato csv
            fputcsv($archivo, [$this->nombre, $this->carnet, $this->correo, $this->edad]);
            fclose($archivo); // cerrando el archivo
            return true;
        }
        return false; // si no se pudo abrir el archivo
    }
}

// aqui es donde se procesan los datos cuando el usuario envia el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // creando un objeto con los datos que se enviaron
    $registro = new RegistroFormulario($_POST["nombre"], $_POST["carnet"], $_POST["correo"], $_POST["edad"]);
    $errores = $registro->validar();

    // si no hay errores, se guarda en el csv
    if (count($errores) === 0) {
        if ($registro->guardarEnArchivo()) {
            echo "<p style='color:green;'>Datos guardados correctamente.</p>";
        } else {
            echo "<p style='color:red;'>Error al guardar los datos.</p>";
        }
    } else {
        // si hay errores, los mostramos en pantalla
        echo "<h3>Se encontraron los siguientes errores:</h3>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li style='color:red;'>$error</li>";
        }
        echo "</ul>";
    }
}
?>
