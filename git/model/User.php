<?php
/** Classe qui représente un utilisateur. Les attributs sont privés. */
class User
{
    private $id;
    private $nom;
    private $email;
    private $password;
    private $role;
    private $createdAt;

    public function __construct($nom = '', $email = '', $password = '', $role = 'citoyen')
    {
        $this->id = 0;
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = '';
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getCreatedAt() { return $this->createdAt; }

    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setRole($role) { $this->role = $role; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }

    // Transforme une ligne SQL en objet User.
    public static function fromArray($row)
    {
        $user = new User();
        $user->setId((int) $row['id']);
        $user->setNom($row['nom']);
        $user->setEmail($row['email']);
        $user->setPassword($row['password']);
        $user->setRole($row['role']);
        $user->setCreatedAt($row['created_at']);
        return $user;
    }
}
