<?php
@session_start();

if (!$_SESSION['user']) {
    exit('<script>location.href = "login.php"</script>');
}

return $_SESSION['user'];