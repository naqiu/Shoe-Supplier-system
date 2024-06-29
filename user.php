<?php
// user.php

interface User {
    public function createUser($conn, $username, $password);
}
?>