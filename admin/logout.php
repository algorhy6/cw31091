<?php
require 'common.php';
logger('logout');
unset($_SESSION['user']);

?>
<script>
    window.location.href = "./login.php"
</script>
