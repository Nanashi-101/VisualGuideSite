<?php
// Admin panel: users list, members CRUD, plans CRUD, contact messages. One page, tabs via ?t=
require 'config.php';
require_admin();

$tabs = ['users' => ['Users', 'people'], 'members' => ['Members', 'person-badge'], 'plans' => ['Plans', 'tags'], 'contacts' => ['Messages', 'envelope']];
$t = isset($tabs[$_GET['t'] ?? '']) ? $_GET['t'] : 'users';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p = $_POST;
    $id = (int)($p['id'] ?? 0);
    try {
        switch ($p['do'] ?? '') {
            case 'delete_user': // an admin cannot delete their own account
                if ($id !== me()['id']) $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
                break;
            case 'delete_contact': $pdo->prepare('DELETE FROM contacts WHERE id=?')->execute([$id]); break;
            case 'delete_plan': $pdo->prepare('DELETE FROM plans WHERE id=?')->execute([$id]); break;
            case 'delete_member': $pdo->prepare('DELETE FROM members WHERE id=?')->execute([$id]); break;
            case 'save_plan':
                if (trim($p['name']) === '' || !is_numeric($p['price'])) { $err = 'Name and numeric price required.'; break; }
                $pdo->prepare('INSERT INTO plans (name,price_eur) VALUES (?,?)')->execute([trim($p['name']), $p['price']]);
                break;
            case 'save_member':
                if (trim($p['first_name']) === '' || trim($p['last_name']) === '') { $err = 'First and last name required.'; break; }
                $v = [trim($p['first_name']), trim($p['last_name']), $p['birthdate'] ?: null, trim($p['address']), trim($p['notes']), (int)$p['plan_id']];
                if ($id) $pdo->prepare('UPDATE members SET first_name=?,last_name=?,birthdate=?,address=?,notes=?,plan_id=? WHERE id=?')->execute([...$v, $id]);
                else $pdo->prepare('INSERT INTO members (first_name,last_name,birthdate,address,notes,plan_id) VALUES (?,?,?,?,?,?)')->execute($v);
                break;
        }
    } catch (PDOException $e) {
        $err = $e->getCode() == 23000 ? 'Cannot delete: it is still in use.' : 'Database error.';
    }
    if (!$err) go("admin.php?t=$t");
}

// Small delete button used in every table.
function del($do, $id) {
    return '<form class="d-inline" method="post" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="do" value="' . $do . '"><input type="hidden" name="id" value="' . (int)$id . '"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';
}
$count = fn($tbl) => (int)$pdo->query("SELECT COUNT(*) FROM $tbl")->fetchColumn(); // $tbl is always a literal below
$stats = [['Users', $count('users'), 'people', '#ff6a00,#ff9a3c'], ['Members', $count('members'), 'person-badge', '#2563eb,#60a5fa'], ['Plans', $count('plans'), 'tags', '#059669,#34d399'], ['Messages', $count('contacts'), 'envelope', '#7c3aed,#a78bfa']];

page_head('Admin'); ?>
<header class="page-head"><div class="container"><span class="eyebrow">Kraftwerk staff</span><h1 class="fw-bold mb-0">Admin panel</h1></div></header>
<div class="container py-5">
  <div class="row g-3 mb-4">
    <?php foreach ($stats as [$label, $n, $ic, $grad]): ?>
    <div class="col-6 col-lg-3"><div class="stat-card" style="background:linear-gradient(135deg,<?= $grad ?>)"><i class="bi bi-<?= $ic ?> fs-3"></i><b><?= $n ?></b><?= $label ?></div></div>
    <?php endforeach; ?>
  </div>

  <ul class="nav nav-pills mb-4 gap-1 flex-nowrap overflow-auto pb-1">
    <?php foreach ($tabs as $k => [$label, $ic]): ?><li class="nav-item"><a class="nav-link text-nowrap <?= $k === $t ? 'active' : '' ?>" href="?t=<?= $k ?>"><i class="bi bi-<?= $ic ?>"></i> <?= $label ?></a></li><?php endforeach; ?>
  </ul>
  <?= alert($err) ?>

<?php if ($t === 'users'):
    $rows = $pdo->query('SELECT u.id,u.email,u.role,u.created_at,COALESCE(f.enabled,0) tfa FROM users u LEFT JOIN user_2fa f ON f.user_id=u.id ORDER BY u.id')->fetchAll(); ?>
  <div class="panel p-3"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>ID</th><th>Email</th><th>Role</th><th>2FA</th><th>Registered</th><th></th></tr></thead>
    <?php foreach ($rows as $r): ?>
    <tr><td><?= $r['id'] ?></td><td><?= h($r['email']) ?></td>
      <td><span class="badge <?= $r['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>"><?= h($r['role']) ?></span></td>
      <td><?= $r['tfa'] ? '<span class="badge bg-success">On</span>' : '<span class="badge bg-warning text-dark">Off</span>' ?></td>
      <td><?= h($r['created_at']) ?></td>
      <td class="text-end"><?= $r['id'] == me()['id'] ? '<span class="text-muted small">(you)</span>' : del('delete_user', $r['id']) ?></td></tr>
    <?php endforeach; ?>
  </table></div></div>

<?php elseif ($t === 'plans'):
    $rows = $pdo->query('SELECT * FROM plans ORDER BY price_eur')->fetchAll(); ?>
  <div class="row g-4">
    <div class="col-lg-4"><div class="panel p-4"><h5 class="fw-bold mb-3">Add plan</h5>
      <form method="post"><input type="hidden" name="do" value="save_plan">
        <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" maxlength="50" required></div>
        <div class="mb-3"><label class="form-label">Price per month (€)</label><input name="price" type="number" step="0.01" min="0" class="form-control" required></div>
        <button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Add</button></form></div></div>
    <div class="col-lg-8"><div class="panel p-3"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
      <thead><tr><th>Name</th><th>€ / month</th><th></th></tr></thead>
      <?php foreach ($rows as $r): ?><tr><td><?= h($r['name']) ?></td><td><?= number_format($r['price_eur'], 2) ?></td><td class="text-end"><?= del('delete_plan', $r['id']) ?></td></tr><?php endforeach; ?>
    </table></div></div></div>
  </div>

<?php elseif ($t === 'members'):
    $plans = $pdo->query('SELECT id,name FROM plans ORDER BY name')->fetchAll();
    $edit = ['id' => 0, 'first_name' => '', 'last_name' => '', 'birthdate' => '', 'address' => '', 'notes' => '', 'plan_id' => 0];
    if (isset($_GET['edit'])) {
        $s = $pdo->prepare('SELECT * FROM members WHERE id=?'); $s->execute([(int)$_GET['edit']]);
        $edit = $s->fetch() ?: $edit;
    }
    $rows = $pdo->query('SELECT m.*, p.name plan FROM members m JOIN plans p ON p.id=m.plan_id ORDER BY m.last_name')->fetchAll(); ?>
  <div class="row g-4">
    <div class="col-lg-4"><div class="panel p-4"><h5 class="fw-bold mb-3"><?= $edit['id'] ? 'Edit member' : 'Add member' ?></h5>
      <form method="post"><input type="hidden" name="do" value="save_member"><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <div class="mb-2"><label class="form-label">Plan</label><select name="plan_id" class="form-select" required>
          <?php foreach ($plans as $pl): ?><option value="<?= $pl['id'] ?>" <?= $pl['id'] == $edit['plan_id'] ? 'selected' : '' ?>><?= h($pl['name']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="row g-2 mb-2">
          <div class="col"><label class="form-label">First name</label><input name="first_name" class="form-control" maxlength="50" value="<?= h($edit['first_name']) ?>" required></div>
          <div class="col"><label class="form-label">Last name</label><input name="last_name" class="form-control" maxlength="50" value="<?= h($edit['last_name']) ?>" required></div>
        </div>
        <div class="mb-2"><label class="form-label">Birthdate</label><input type="date" name="birthdate" class="form-control" value="<?= h($edit['birthdate']) ?>"></div>
        <div class="mb-2"><label class="form-label">Address</label><input name="address" class="form-control" maxlength="150" value="<?= h($edit['address']) ?>"></div>
        <div class="mb-3"><label class="form-label">Notes</label><input name="notes" class="form-control" maxlength="100" value="<?= h($edit['notes']) ?>"></div>
        <button class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Save</button>
        <?php if ($edit['id']): ?><a href="?t=members" class="btn btn-link w-100">Cancel edit</a><?php endif; ?></form></div></div>
    <div class="col-lg-8"><div class="panel p-3"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
      <thead><tr><th>Name</th><th>Birthdate</th><th>Address</th><th>Plan</th><th>Notes</th><th></th></tr></thead>
      <?php foreach ($rows as $r): ?>
      <tr><td><?= h($r['first_name'] . ' ' . $r['last_name']) ?></td><td><?= h($r['birthdate']) ?></td><td><?= h($r['address']) ?></td><td><span class="badge bg-primary"><?= h($r['plan']) ?></span></td><td><?= h($r['notes']) ?></td>
          <td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="?t=members&edit=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a> <?= del('delete_member', $r['id']) ?></td></tr>
      <?php endforeach; ?>
    </table></div></div></div>
  </div>

<?php else:
    $rows = $pdo->query('SELECT * FROM contacts ORDER BY id DESC')->fetchAll(); ?>
  <div class="panel p-3"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Date</th><th>Email</th><th>Subject</th><th>Message</th><th></th></tr></thead>
    <?php foreach ($rows as $r): ?><tr><td class="text-nowrap"><?= h($r['created_at']) ?></td><td><?= h($r['email']) ?></td><td><?= h($r['subject']) ?></td><td><?= h($r['message']) ?></td><td class="text-end"><?= del('delete_contact', $r['id']) ?></td></tr><?php endforeach; ?>
  </table></div></div>
<?php endif; ?>
</div>
<?php page_foot();
