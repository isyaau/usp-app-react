<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use App\Models\HistoryLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('role', function ($app) {
            return new RoleMiddleware(); // pastikan Role ada di namespace yang benar
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('userLogin', Auth::guard('web')->user());
        });
        Schema::defaultStringLength(191);

        Event::listen('eloquent.created: *', fn (string $event, array $payload) => $this->handleModelEvent($payload[0], 'create'));
        Event::listen('eloquent.updated: *', fn (string $event, array $payload) => $this->handleModelEvent($payload[0], 'update'));
        Event::listen('eloquent.deleted: *', fn (string $event, array $payload) => $this->handleModelEvent($payload[0], 'delete'));
    }

    /**
     * Jembatan listener → pencatatan. Tidak mencatat saat berjalan di
     * lingkungan test (PHPUnit) agar log tidak dipenuhi data uji.
     */
    private function handleModelEvent(Model $model, string $action): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $this->recordHistory($model, $action);
    }

    /**
     * Catat perubahan data ke tabel history_logs saat model Eloquent
     * dibuat, diubah, atau dihapus oleh user yang sedang login.
     *
     * Tidak mencatat aktivitas seeding/console tanpa user login, perubahan
     * pada HistoryLog itu sendiri, maupun aksi yang tidak mengubah data.
     */
    public function recordHistory(Model $model, string $action): void
    {
        if ($model instanceof HistoryLog) {
            return;
        }

        if (! Auth::check()) {
            return;
        }

        $changes = [];

        if ($action === 'create') {
            foreach ($model->getAttributes() as $key => $value) {
                if (in_array($key, ['created_at', 'updated_at'], true)) {
                    continue;
                }
                $changes[$key] = ['new' => $value];
            }
        } elseif ($action === 'update') {
            foreach ($model->getChanges() as $key => $value) {
                if (in_array($key, ['created_at', 'updated_at'], true)) {
                    continue;
                }
                $changes[$key] = [
                    'old' => $model->getOriginal($key),
                    'new' => $value,
                ];
            }
        } else { // delete
            foreach ($model->getAttributes() as $key => $value) {
                if (in_array($key, ['created_at', 'updated_at'], true)) {
                    continue;
                }
                $changes[$key] = ['old' => $value];
            }
        }

        if ($changes === []) {
            return;
        }

        HistoryLog::create([
            'user_id' => Auth::id(),
            'table' => $model->getTable(),
            'record_id' => $model->getKey() ?? 0,
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
