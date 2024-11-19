<?php 

function getUsername($db, $user_id) {
    $query = "SELECT username FROM user WHERE user_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchColumn();

    return $result;
}


/**
 * Adds new user to database.
 */
function addUser($db, $username, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // using bcrypt hashing algorithm
    $defaultRole = "USER";

    $query = "INSERT INTO user(username, password, email, role) VALUES (:username, :password, :email, :role)";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", trim($username));
    $statement->bindValue(":email", trim($email));
    $statement->bindValue(":password", $hashedPassword);
    $statement->bindValue(":role", $defaultRole);

    return $statement->execute();
}

function getRole($db, $user_id) {
    $query = "SELECT role FROM user WHERE user_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchColumn();

    return $result;
}

?>