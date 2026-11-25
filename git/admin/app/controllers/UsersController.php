<?php
/** Gestion des utilisateurs dans le backoffice (CRUD). */
require_once __DIR__ . '/../../../Controllers/UserService.php';
require_once __DIR__ . '/../../../Controllers/Validator.php';
require_once __DIR__ . '/../../../Controllers/AccessController.php';

class UsersController extends Controller
{
    protected $page = 'utilisateurs';
    protected $title = 'Utilisateurs | LifeBridge';
    private $users;

    public function __construct()
    {
        $this->users = new UserService();
    }

    // READ : afficher la liste des utilisateurs.
    public function index()
    {
        $this->render('users_list', array(
            'users' => $this->users->getAll(),
            'currentId' => isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0,
            'csrfToken' => Csrf::token()
        ));
    }

    // CREATE : afficher et traiter le formulaire d'ajout.
    public function create()
    {
        $errors = array();
        $old = array('role' => 'citoyen');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAndCreate($_POST);
            $old = array(
                'nom' => isset($_POST['nom']) ? $_POST['nom'] : '',
                'email' => isset($_POST['email']) ? $_POST['email'] : '',
                'role' => isset($_POST['role']) ? $_POST['role'] : 'citoyen'
            );

            if (empty($errors)) {
                setFlash('Utilisateur créé avec succès.');
                $this->redirectList();
            }
        }

        $this->render('user_form', array(
            'editUser' => null,
            'errors' => $errors,
            'old' => $old,
            'csrfToken' => Csrf::token()
        ));
    }

    // UPDATE : afficher et traiter le formulaire de modification.
    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $editUser = $this->users->findById($id);

        if ($editUser === null) {
            setFlash('Utilisateur introuvable.', 'danger');
            $this->redirectList();
        }

        $errors = array();
        $old = array(
            'nom' => $editUser->getNom(),
            'email' => $editUser->getEmail(),
            'role' => $editUser->getRole()
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAndUpdate($id, $_POST);
            $old = array(
                'nom' => isset($_POST['nom']) ? $_POST['nom'] : '',
                'email' => isset($_POST['email']) ? $_POST['email'] : '',
                'role' => isset($_POST['role']) ? $_POST['role'] : $editUser->getRole()
            );

            if (empty($errors)) {
                setFlash('Utilisateur modifié avec succès.');
                $this->redirectList();
            }
        }

        $this->render('user_form', array(
            'editUser' => $editUser,
            'errors' => $errors,
            'old' => $old,
            'csrfToken' => Csrf::token()
        ));
    }

    // DELETE : la suppression accepte uniquement une requête POST avec jeton CSRF.
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée.');
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;

        if (!Csrf::verify($token)) {
            setFlash('Jeton de sécurité invalide.', 'danger');
        } elseif ($id === (int) $_SESSION['user_id']) {
            setFlash('Vous ne pouvez pas supprimer votre propre compte.', 'danger');
        } else {
            $this->users->delete($id);
            setFlash('Utilisateur supprimé.');
        }

        $this->redirectList();
    }

    private function validateAndCreate($post)
    {
        if (!Csrf::verify(isset($post['csrf_token']) ? $post['csrf_token'] : null)) {
            return array('Jeton de sécurité invalide. Veuillez réessayer.');
        }

        $errors = Validator::user($post, true);
        $role = isset($post['role']) && is_string($post['role']) ? $post['role'] : '';
        if (!isset(AccessController::$LABELS[$role])) {
            $errors[] = 'Rôle invalide.';
        }
        if (!empty($errors)) {
            return $errors;
        }

        $email = trim($post['email']);
        if ($this->users->emailExists($email)) {
            return array('Cette adresse e-mail est déjà utilisée.');
        }

        $user = new User(trim($post['nom']), $email, password_hash($post['password'], PASSWORD_DEFAULT), $role);
        if ($this->users->insert($user)) {
            return array();
        }
        return array('Erreur lors de la création de l’utilisateur.');
    }

    private function validateAndUpdate($id, $post)
    {
        if (!Csrf::verify(isset($post['csrf_token']) ? $post['csrf_token'] : null)) {
            return array('Jeton de sécurité invalide. Veuillez réessayer.');
        }

        $errors = Validator::user($post, false);
        $role = isset($post['role']) && is_string($post['role']) ? $post['role'] : '';
        if (!isset(AccessController::$LABELS[$role])) {
            $errors[] = 'Rôle invalide.';
        }
        // L'administrateur ne peut pas retirer son propre accès au backoffice.
        if ($id === (int) $_SESSION['user_id'] && $role !== 'securite') {
            $errors[] = 'Vous ne pouvez pas modifier votre propre rôle.';
        }
        if (!empty($errors)) {
            return $errors;
        }

        $email = trim($post['email']);
        if ($this->users->emailExists($email, $id)) {
            return array('Cette adresse e-mail est déjà utilisée.');
        }

        $password = isset($post['password']) ? $post['password'] : '';
        $hash = null;
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->users->update($id, trim($post['nom']), $email, $role, $hash)) {
            return array();
        }
        return array('Erreur lors de la modification.');
    }

    private function redirectList()
    {
        header('Location: index.php?page=utilisateurs');
        exit;
    }
}
