<?php
session_start();
session_destroy(); // Уничтожаем сессию
header("Location: ../../index.php");
exit;
