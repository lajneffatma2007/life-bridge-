<?php /** Liste des utilisateurs (Read) — Attend : $users, $currentId, $csrfToken */ ?>
<section class="p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Utilisateurs</h2>
    <a href="index.php?page=utilisateurs&amp;do=create" class="btn btn-danger"><i class="bi bi-plus-lg"></i> Ajouter</a>
  </div>
  <div class="panel"><table class="table table-hover align-middle">
    <thead><tr><th>ID</th><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Créé le</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u->getId() ?></td>
        <td><?= e($u->getNom()) ?></td>
        <td><?= e($u->getEmail()) ?></td>
        <td><span class="badge text-bg-secondary"><?= e(isset(AccessController::$LABELS[$u->getRole()]) ? AccessController::$LABELS[$u->getRole()] : $u->getRole()) ?></span></td>
        <td><?= e($u->getCreatedAt()) ?></td>
        <td class="d-flex gap-2">
          <a href="index.php?page=utilisateurs&amp;do=edit&amp;id=<?= $u->getId() ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
          <?php if ($u->getId() !== $currentId): ?>
          <form method="POST" action="index.php?page=utilisateurs&amp;do=delete" data-confirm="Supprimer cet utilisateur ?">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="hidden" name="id" value="<?= $u->getId() ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
