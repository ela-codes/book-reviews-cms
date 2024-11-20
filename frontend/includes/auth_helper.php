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
function addUser($db, $username, $email, $password, $created_by) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // using bcrypt hashing algorithm
    $defaultRole = "USER";

    $query = "INSERT INTO user(username, password, email, role, created_by) VALUES (:username, :password, :email, :role, :created_by)";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", trim($username));
    $statement->bindValue(":email", trim($email));
    $statement->bindValue(":password", $hashedPassword);
    $statement->bindValue(":role", $defaultRole);
    $statement->bindValue(":created_by", $created_by);

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

function checkValidUsername($username)
{
    $username = trim($username); // Remove leading and trailing whitespace
    return $username !== "" && preg_match('/^[a-zA-Z0-9]+$/', $username) === 1;
}

function checkUserExists($db, $username, $email)
{

    $statement = $db->prepare("SELECT COUNT(*) FROM user WHERE username = :username AND email = :email");
    $statement->bindValue(":username", strtolower(trim($username)));
    $statement->bindValue(":email", strtolower(trim($email)));

    $statement->execute();

    $result = $statement->fetchColumn();

    return $result > 0;
}

?>