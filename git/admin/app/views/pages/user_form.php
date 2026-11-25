<?php
/** Formulaire Create / Update — Attend : $editUser (User|null), $errors, $old, $csrfToken */
$isEdit = $editUser !== null;
$action = $isEdit
    ? 'index.php?page=utilisateurs&do=edit&id=' . $editUser->getId()
    : 'index.php?page=utilisateurs&do=create';
$selectedRole = isset($old['role']) ? $old['role'] : 'citoyen';
?>
<section class="p-4">
  <h2 class="fw-bold mb-3"><?= $isEdit ? "Modifier l'utilisateur" : "Ajouter un utilisateur" ?></h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="panel"><form method="POST" action="<?= e($action) ?>" data-validate="user"
        data-password-required="<?= $isEdit ? '0' : '1' ?>"
        data-exclude-id="<?= $isEdit ? $editUser->getId() : 0 ?>"
 novalidate>
    <div class="alert alert-danger js-errors" hidden></div>
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

    <label class="form-label">Nom complet</label>
    <input name="nom" class="form-control mb-3" required value="<?= e((isset($old['nom']) ? $old['nom'] : '')) ?>">

    <label class="form-label">Adresse e-mail</label>
    <input type="email" name="email" class="form-control mb-3" required value="<?= e((isset($old['email']) ? $old['email'] : '')) ?>">

    <label class="form-label">Rôle</label>
    <select name="role" class="form-select mb-3">
      <?php foreach (AccessController::$LABELS as $key => $label): ?>
        <option value="<?= e($key) ?>" <?= $selectedRole === $key ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>

    <label class="form-label">Mot de passe <?= $isEdit ? '(laisser vide pour ne pas changer)' : '(8 caractères minimum)' ?></label>
    <input type="password" name="password" class="form-control mb-3" <?= $isEdit ? '' : 'required' ?> autocomplete="new-password">

    <label class="form-label">Confirmer le mot de passe</label>
    <input type="password" name="password_confirm" class="form-control mb-3" autocomplete="new-password">

    <button type="submit" class="btn btn-danger"><?= $isEdit ? 'Enregistrer' : 'Créer' ?></button>
    <a href="index.php?page=utilisateurs" class="btn btn-outline-secondary">Annuler</a>
  </form></div>
</section>
