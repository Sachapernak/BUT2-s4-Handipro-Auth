<?php
require_once __DIR__ . '/../Utils/AuthDatabase.php';

use Auth\Utils\AuthDatabase;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = AuthDatabase::getInstance()->getConnection();
    }

    /**
     * Recherche un utilisateur par son nom d'utilisateur.
     *
     * @param string $username
     * @return array|false Renvoie le tableau associatif de l'utilisateur ou false si non trouvé.
     */
    public function findByUsername(string $username) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE login = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche un utilisateur par son identifiant.
     *
     * @param int $id
     * @return array|false Renvoie le tableau associatif de l'utilisateur ou false si non trouvé.
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * @param string $username
     * @param string $plainPassword Le mot de passe en clair qui sera hashé.
     * @param string $role
     * @return string|false Renvoie l'ID du nouvel utilisateur ou false en cas d'échec.
     */
    public function create(string $username, string $plainPassword, string $role) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO user (login, password, role) VALUES (:username, :hashed_password, :role)"
        );
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashed_password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}