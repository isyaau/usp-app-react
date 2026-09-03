<?php

use App\Models\AccGroup;
use App\Models\HistoryLog;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Pembuatan/ubah/hapus data oleh user login tercatat di history_logs.
 * Listener otomatis sengaja dilewati saat test (PHPUnit) agar tidak menulis
 * data uji ke log, sehingga logika diuji langsung lewat recordHistory().
 */

function historyTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'history-test@ksp.test'],
        [
            'nama' => 'History Tester',
            'username' => 'historytester',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]
    );
}

it('mencatat create, update, dan delete ke history_logs', function () {
    $user = historyTestUser();

    $this->actingAs($user);
    expect(Auth::check())->toBeTrue();

    $recorder = app()->getProvider(AppServiceProvider::class);

    $group = AccGroup::create(['nama' => 'TEST-HISTORY-LOG', 'user_id' => $user->id]);
    $groupId = $group->id;
    $recorder->recordHistory($group, 'create');

    $group->update(['nama' => 'TEST-HISTORY-LOG-2']);
    $recorder->recordHistory($group, 'update');

    $group->delete();
    $recorder->recordHistory($group, 'delete');

    $logs = HistoryLog::query()
        ->where('table', 'acc_group')
        ->where('record_id', $groupId)
        ->orderBy('id')
        ->get();

    expect($logs)->toHaveCount(3)
        ->and($logs->pluck('action')->all())->toBe(['create', 'update', 'delete'])
        ->and($logs->pluck('user_id')->all())->toBe([$user->id, $user->id, $user->id])
        ->and($logs[0]->changes['nama'])->toBe(['new' => 'TEST-HISTORY-LOG'])
        ->and($logs[1]->changes['nama']['new'])->toBe('TEST-HISTORY-LOG-2')
        ->and($logs[1]->changes['nama'])->toHaveKeys(['old', 'new'])
        ->and($logs[2]->changes['nama'])->toBe(['old' => 'TEST-HISTORY-LOG-2']);

    HistoryLog::query()->where('table', 'acc_group')->where('record_id', $groupId)->delete();
});

it('tidak mencatat perubahan tanpa user login', function () {
    $recorder = app()->getProvider(AppServiceProvider::class);

    $group = AccGroup::create(['nama' => 'TEST-HISTORY-NOAUTH', 'user_id' => null]);
    $group->delete();

    $recorder->recordHistory($group, 'create');
    $recorder->recordHistory($group, 'delete');

    expect(HistoryLog::query()->where('table', 'acc_group')->where('record_id', $group->id)->count())->toBe(0);

    HistoryLog::query()->where('table', 'acc_group')->where('record_id', $group->id)->delete();
});