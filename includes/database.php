<?php
require_once(LIB_PATH_INC.DS."config.php");

class MySqli_DB {

    private $con;
    public $query_id;

    // Constructor para la conexión a la base de datos
    function __construct() {
      $this->db_connect();
    }

    /*--------------------------------------------------------------*/
    /* Función para abrir la conexión a la base de datos
    /*--------------------------------------------------------------*/
    public function db_connect() {
        $this->con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->con) {
            die("Conexión a la base de datos fallida: " . mysqli_connect_error());
        }
    }

    /*--------------------------------------------------------------*/
    /* Función para cerrar la conexión a la base de datos
    /*--------------------------------------------------------------*/
    public function db_disconnect() {
        if (isset($this->con)) {
            mysqli_close($this->con);
            unset($this->con);
        }
    }

    /*--------------------------------------------------------------*/
    /* Función para ejecutar una consulta simple
    /*--------------------------------------------------------------*/
    public function query($sql) {
        if (trim($sql != "")) {
            $this->query_id = $this->con->query($sql);
        }
        if (!$this->query_id) {
            // Solo para modo de desarrollo
            die("Error en esta consulta :<pre> " . $sql ."</pre>");
        }
        return $this->query_id;
    }

    /*--------------------------------------------------------------*/
    /* Función para obtener los resultados de una consulta
    /*--------------------------------------------------------------*/
    public function fetch_array($statement) {
        return mysqli_fetch_array($statement);
    }

    public function fetch_object($statement) {
        return mysqli_fetch_object($statement);
    }

    public function fetch_assoc($statement) {
        return mysqli_fetch_assoc($statement);
    }

    public function num_rows($statement) {
        return mysqli_num_rows($statement);
    }

    public function insert_id() {
        return mysqli_insert_id($this->con);
    }

    public function affected_rows() {
        return mysqli_affected_rows($this->con);
    }

    /*--------------------------------------------------------------*/
    /* Función para escapar caracteres especiales
    /* para evitar inyecciones SQL
    /*--------------------------------------------------------------*/
    public function escape($str) {
        return $this->con->real_escape_string($str);
    }

    /*--------------------------------------------------------------*/
    /* Función para ciclo while
    /*--------------------------------------------------------------*/
    public function while_loop($loop) {
        $results = array();
        while ($result = $this->fetch_array($loop)) {
            $results[] = $result;
        }
        return $results;
    }

    /*--------------------------------------------------------------*/
    /* Función para iniciar una transacción
    /*--------------------------------------------------------------*/
    public function begin_transaction() {
        return $this->con->begin_transaction();
    }

    /*--------------------------------------------------------------*/
    /* Función para confirmar la transacción
    /*--------------------------------------------------------------*/
    public function commit() {
        return $this->con->commit();
    }

    /*--------------------------------------------------------------*/
    /* Función para revertir la transacción
    /*--------------------------------------------------------------*/
    public function rollback() {
        return $this->con->rollback();
    }

    /*--------------------------------------------------------------*/
    /* Función para ejecutar consultas preparadas
    /*--------------------------------------------------------------*/
    public function prepare($sql) {
        return $this->con->prepare($sql);
    }

    /*--------------------------------------------------------------*/
    /* Función para ejecutar una consulta preparada
    /*--------------------------------------------------------------*/
    public function execute($stmt) {
        return $stmt->execute();
    }

    /*--------------------------------------------------------------*/
    /* Función para vincular parámetros a una consulta preparada
    /*--------------------------------------------------------------*/
    public function bind_param($stmt, $types, ...$params) {
        return $stmt->bind_param($types, ...$params);
    }

}

$db = new MySqli_DB();

?>
