<?php 
function getUsername($db, $user_id) {
    $query = "SELECT username FROM user WHERE user_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $user_id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchColumn();

    return $result;
}

?>