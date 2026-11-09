<?php

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

function userWithRole(string $role): User
{
    return User::factory()->create()->assignRole($role);
}

function validMahasiswaPayload(array $overrides = []): array
{
    return [
        'nrp' => '5025211001',
        'nama' => 'Budi Santoso',
        'departemen' => Mahasiswa::DEPARTEMEN[0],
        'dosen_id' => Dosen::factory()->create()->id,
        'is_active' => '1',
        ...$overrides,
    ];
}

test('guests are redirected to login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('users without a role cannot open the admin panel by url', function () {
    $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
});

test('unverified users are sent to the verification notice', function () {
    $user = User::factory()->unverified()->create()->assignRole('Admin');

    $this->actingAs($user)->get(route('admin.dashboard'))->assertRedirect(route('verification.notice'));
});

test('login destination depends on the role', function (?string $role, string $destination) {
    $user = $role ? userWithRole($role) : User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route($destination));
})->with([
    'admin' => ['Admin', 'admin.dashboard'],
    'staf' => ['Staf', 'admin.dashboard'],
    'no role' => [null, 'home'],
]);

test('dashboard widgets show live counts and exclude expired tugas', function () {
    Mahasiswa::factory(2)->create();
    Tugas::factory()->create(['tenggat' => today()->format('Y-m-d')]);
    Tugas::factory()->create(['tenggat' => today()->subDay()->format('Y-m-d')]);

    $this->actingAs(userWithRole('Staf'))->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('totalMahasiswa', 2)
        ->assertViewHas('totalDosen', 2)
        ->assertViewHas('totalTugasAktif', 1);
});

test('admin can delete mahasiswa', function () {
    $mahasiswa = Mahasiswa::factory()->create();

    $this->actingAs(userWithRole('Admin'))->delete(route('admin.mahasiswa.destroy', $mahasiswa))->assertRedirect();

    $this->assertModelMissing($mahasiswa);
});

test('staf is refused when deleting mahasiswa directly', function () {
    $mahasiswa = Mahasiswa::factory()->create();

    $this->actingAs(userWithRole('Staf'))->delete(route('admin.mahasiswa.destroy', $mahasiswa))->assertForbidden();

    $this->assertModelExists($mahasiswa);
});

test('delete button is only rendered for admin', function (string $role, bool $isVisible) {
    $mahasiswa = Mahasiswa::factory()->create();
    $deleteForm = 'action="'.route('admin.mahasiswa.destroy', $mahasiswa).'"';

    $response = $this->actingAs(userWithRole($role))->get(route('admin.mahasiswa.index'))->assertOk();

    $isVisible ? $response->assertSee($deleteForm, false) : $response->assertDontSee($deleteForm, false);
})->with([
    'admin' => ['Admin', true],
    'staf' => ['Staf', false],
]);

test('staf can create mahasiswa', function () {
    $this->actingAs(userWithRole('Staf'))
        ->post(route('admin.mahasiswa.store'), validMahasiswaPayload())
        ->assertRedirect(route('admin.mahasiswa.index'));

    $this->assertDatabaseHas('mahasiswas', ['nrp' => '5025211001', 'nama' => 'Budi Santoso']);
});

test('create and edit forms render', function () {
    $staf = userWithRole('Staf');
    $mahasiswa = Mahasiswa::factory()->create();

    $this->actingAs($staf)->get(route('admin.mahasiswa.create'))->assertOk();
    $this->actingAs($staf)->get(route('admin.mahasiswa.edit', $mahasiswa))->assertOk()->assertSee($mahasiswa->nrp);
});

test('nrp must be unique except for the record being edited', function () {
    $staf = userWithRole('Staf');
    $mahasiswa = Mahasiswa::factory()->create();
    $payload = validMahasiswaPayload(['nrp' => $mahasiswa->nrp]);

    $this->actingAs($staf)->post(route('admin.mahasiswa.store'), $payload)->assertInvalid(['nrp']);
    $this->actingAs($staf)->put(route('admin.mahasiswa.update', $mahasiswa), $payload)->assertValid();

    expect($mahasiswa->refresh()->nama)->toBe('Budi Santoso');
});

test('mahasiswa payload is validated', function () {
    $this->actingAs(userWithRole('Staf'))
        ->post(route('admin.mahasiswa.store'), [])
        ->assertInvalid(['nrp', 'nama', 'departemen', 'dosen_id', 'is_active']);
});

test('search matches nama or nrp', function () {
    $byName = Mahasiswa::factory()->create(['nama' => 'Siti Aminah', 'nrp' => '5025000001']);
    $byNrp = Mahasiswa::factory()->create(['nama' => 'Andi Wijaya', 'nrp' => '5025999999']);
    Mahasiswa::factory()->create(['nama' => 'Rina Kusuma', 'nrp' => '5025000002']);

    $admin = userWithRole('Admin');

    $this->actingAs($admin)->get(route('admin.mahasiswa.index', ['search' => 'Aminah']))
        ->assertViewHas('mahasiswas', fn ($page) => $page->pluck('id')->all() === [$byName->id]);
    $this->actingAs($admin)->get(route('admin.mahasiswa.index', ['search' => '999999']))
        ->assertViewHas('mahasiswas', fn ($page) => $page->pluck('id')->all() === [$byNrp->id]);
});

test('status filter also works for the falsy cuti value', function () {
    $cuti = Mahasiswa::factory()->create(['is_active' => false]);
    Mahasiswa::factory()->create(['is_active' => true]);

    $this->actingAs(userWithRole('Staf'))->get(route('admin.mahasiswa.index', ['status' => '0']))
        ->assertViewHas('mahasiswas', fn ($page) => $page->pluck('id')->all() === [$cuti->id]);
});

test('departemen filter narrows the list', function () {
    $match = Mahasiswa::factory()->create(['departemen' => 'Sistem Informasi']);
    Mahasiswa::factory()->create(['departemen' => 'Teknik Elektro']);

    $this->actingAs(userWithRole('Staf'))->get(route('admin.mahasiswa.index', ['departemen' => 'Sistem Informasi']))
        ->assertViewHas('mahasiswas', fn ($page) => $page->pluck('id')->all() === [$match->id]);
});

test('malformed filter input is rejected instead of crashing', function () {
    $this->actingAs(userWithRole('Staf'))
        ->get(route('admin.mahasiswa.index', ['search' => ['array']]))
        ->assertInvalid(['search']);
});

test('pagination links keep the active filters', function () {
    Mahasiswa::factory(11)->create(['nama' => 'Budi Pratama']);

    $this->actingAs(userWithRole('Staf'))->get(route('admin.mahasiswa.index', ['search' => 'Budi']))
        ->assertViewHas('mahasiswas', fn ($page) => str_contains($page->url(2), 'search=Budi'));
});

test('demo login buttons only appear in the local environment', function () {
    $this->get(route('login'))->assertDontSee('Login Admin');

    app()->detectEnvironment(fn () => 'local');

    $this->get(route('login'))->assertSee('Login Admin')->assertSee('Login Staf');
});

test('database seeder creates demo accounts and dozens of records', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::firstWhere('email', 'admin@example.com')->can('hapus-mahasiswa'))->toBeTrue()
        ->and(User::firstWhere('email', 'staf@example.com')->can('hapus-mahasiswa'))->toBeFalse()
        ->and(Mahasiswa::count())->toBe(60)
        ->and(Dosen::count())->toBe(10)
        ->and(Tugas::count())->toBe(20);
});
