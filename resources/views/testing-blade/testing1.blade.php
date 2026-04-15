CONTOH PEMAKAIAN DI CONTROLLER
🏠 HOME
public function home()
{
logView(
'home',
'Home'
);

return view('home', [
'title' => 'Home'
]);
}

📄 PAGE DENGAN DATA (DETAIL)
public function show(User $user)
{
logView(
'user_detail',
'Detail User',
[
'data_id' => $user->id,
'data_type' => 'User',
]
);

return view('user.show', compact('user'));
}



✅ CONTOH PEMAKAIAN DI CONTROLLER
➕ STORE
$user = User::create($request->all());

logCreate(
'user',
'User Management',
$user
);

✏️ UPDATE
$oldData = $user->getOriginal();

$user->update($request->all());

logUpdate(
'user',
'User Management',
$user,
$oldData
);

🗑 DELETE
logDelete(
'user',
'User Management',
$user
);

$user->delete();









STRUKTUR PROPERTY (STANDARD)

Kita pakai pola ini untuk SEMUA activity:
[
    'page'        => '',
    'page_title'  => '',
    'action'      => '',     // create | update | delete
    'data_id'     => null,   // ID data utama
    'data_type'   => '',     // table / model name
    'old'         => [],     // update/delete
    'new'         => [],     // create/update
]



🟢 1️⃣ TEMPLATE INSERT / STORE

📍 Biasanya di store() controller
// create Activity
logActivity(
    'Create Data',
    $model, // subject (model yang baru dibuat)
    [
        'page'        => 'home',
        'page_title'  => 'Home',
        'action'      => 'create',
        'data_id'     => $model->id,
        'data_type'   => 'users', // atau nama model
        'new'         => $model->toArray(),
    ],
    'created'
);



2️⃣ TEMPLATE UPDATE

📍 Di update() controller

$oldData = $model->getOriginal();

// update data
$model->update($request->all());

// update Activity
logActivity(
    'Update Data',
    $model,
    [
        'page'        => 'home',
        'page_title'  => 'Home',
        'action'      => 'update',
        'data_id'     => $model->id,
        'data_type'   => 'users',
        'old'         => $oldData,
        'new'         => $model->fresh()->toArray(),
    ],
    'updated'
);



🔴 3️⃣ TEMPLATE DELETE

📍 Di destroy() controller
$oldData = $model->toArray();

// delete data
$model->delete();

// delete Activity
logActivity(
    'Delete Data',
    null, // subject null karena data sudah dihapus
    [
        'page'        => 'home',
        'page_title'  => 'Home',
        'action'      => 'delete',
        'data_id'     => $oldData['id'],
        'data_type'   => 'users',
        'old'         => $oldData,
    ],
    'deleted'
);


🧠 CATATAN PENTING (KENAPA BEGINI)
🔹 subject
Action	Subject
viewed	null
create	$model
update	$model
delete	null (aman)


🔹 event (Spatie)

Gunakan kata kerja lampau:

created | updated | deleted | viewed



📌 CONTOH FINAL YANG IDEAL DI DB
{
  "description": "Update Data",
  "event": "updated",
  "properties": {
    "page": "home",
    "page_title": "Home",
    "action": "update",
    "data_id": 12,
    "data_type": "users",
    "old": { "name": "A" },
    "new": { "name": "B" }
  }
}
