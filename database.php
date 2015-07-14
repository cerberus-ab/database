<?php

    class DataBase {

        // дискриптор соединения
        private $conn;

        // настройки соединения
        private $options = array(
            // адрес сервера базы данных
            'host' => 'localhost',
            // логин для входа
            'user' => 'root',
            // пароль для входа
            'pass' => '',
            // используемая таблица
            'base' => '',
            // используемая кодировка
            'charset' => 'utf8',
            // название дискриптора соединения
            'name' => null,
            // режим работы fetch: MYSQLI_ASSOC | MYSQLI_NUM
            'result_mode' => MYSQLI_ASSOC
        );

        /**
         * Функция вывода ошибки
         * @param  [string] $err - текст ошибки
         * @return [exception] исключение
         */
        private function error($err) {
            throw new Exception((!is_null($this->options["name"]) ?$this->options["name"].": " :"").$err);
        }

        /**
         * Выполнить запрос в базу данных
         * @param  [string] $query - строка запроса
         * @return [mysqli_result] результат выполнения запроса
         */
        private function query($query) {
            // попытка выполнить запрос
            if (!$result = mysqli_query($this->conn, $query)) {
                $this->error(mysqli_error($this->conn));
            }
            // вернуть результат
            return $result;
        }

        /**
         * Конструктор соединения с базой данных
         * @param [array] $options - параметры подключения
         */
        function __construct($options = array()) {
            // настройки по умолчанию
            $this->options = array_merge($this->options, $options);
            // попытка установить соединение с базой данных
            if (!@$this->conn = mysqli_connect($options['host'], $options['user'], $options['pass'], $options['base'])) {
                $this->error(mysqli_connect_error());
            }
            // установить кодировку
            mysqli_set_charset($this->conn, $this->options['charset']) or $this->error(mysqli_error($this->conn));
        }

        /**
         * Деструктор соединения с базой данных
         */
        public function Destroy() {
            mysqli_close($this->conn) or $this->error(mysqli_error($this->conn));
        }

        /**
         * Получить значение поля строки выполненного запроса
         * @param  [string] $query - строка запроса
         * @return [misk] результат
         */
        public function getValue($query) {
            // если запрос выполнился успешно, то обработать
            if ($result = $this->query($query)) {
                $row = mysqli_fetch_array($result, $this->options["result_mode"]);
                mysqli_free_result($result);
                return reset($row);
            }
            // иначе вернуть false
            return false;
        }

        /**
         * Получить одну строку выполненного запроса
         * @param  [string] $query - строка запроса
         * @return [array] результат как ассоциативный массив
         */
        public function getRow($query) {
            // если запрос выполнился успешно, то обработать
            if ($result = $this->query($query)) {
                $row = mysqli_fetch_array($result, $this->options["result_mode"]);
                mysqli_free_result($result);
                return $row;
            }
            // иначе вернуть false
            return false;
        }

        /**
         * Получить все строки выполненного запроса
         * @param  [string] $query - строка запроса
         * @return [array] результат как ассоциативный массив
         */
        public function getAll($query) {
            $collection = array();
            // если запрос выполнился успешно, то обработать
            if ($result = $this->query($query)) {
                while ($row = mysqli_fetch_array($result, $this->options["result_mode"])) {
                    $collection[] = $row;
                }
                mysqli_free_result($result);
            }
            // вернуть коллекцию
            return $collection;
        }

    }

?>
