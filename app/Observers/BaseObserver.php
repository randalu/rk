<?php

namespace App\Observers;

use App\Models\ActionLog;
use Illuminate\Database\Eloquent\Model;

class BaseObserver
{
    protected function log(
        Model  $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            ActionLog::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'model'       => class_basename($model),
                'record_id'   => $model->getKey(),
                'old_values'  => $oldValues,
                'new_values'  => $newValues,
                'description' => $this->describe($action, $model),
                'created_at'  => now(),
            ]);
        } catch (\Exception $e) {
            // Never let logging break the app
        }
    }

    protected function describe(string $action, Model $model): string
    {
        $name  = class_basename($model);
        $id    = $model->getKey();
        $label = $this->getLabel($model);

        return ucfirst($action) . " {$name} #{$id}" . ($label ? " — {$label}" : '');
    }

    protected function getLabel(Model $model): string
    {
        // Try common name fields
        foreach (['name', 'title', 'number'] as $field) {
            if (isset($model->$field)) {
                return $model->$field;
            }
        }
        return '';
    }
}