<?php
$connection = mysqli_connect('localhost:8889', 'root', 'root', 'wordpress_local');
if (!$connection) {
    die('Ошибка подключения: ' . mysqli_connect_error());
} else {
    echo 'Подключение успешно!';
}
mysqli_close($connection);
?>