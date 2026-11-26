<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Base class for every dashboard assistant tool.
 *
 * Each tool acts on behalf of one signed-in admin and declares the Spatie
 * permission it needs. The agent only offers a tool to the model when the
 * admin holds that permission, and the check is repeated at execution time
 * so a tool can never do more than the admin could do by hand.
 */
abstract class DashboardTool implements Tool
{
    public function __construct(protected User $user) {}

    /**
     * Permission required to use the tool; null when any assistant user may.
     */
    abstract public function permission(): ?string;

    /**
     * Perform the tool's work once authorization passed.
     */
    abstract protected function run(Request $request): string;

    /**
     * Whether the acting admin may use this tool.
     */
    public function isAvailable(): bool
    {
        $permission = $this->permission();

        return $permission === null || $this->user->can($permission);
    }

    /**
     * Execute the tool with authorization and friendly error reporting, so
     * the model can recover instead of the whole turn failing.
     */
    public function handle(Request $request): Stringable|string
    {
        if (! $this->isAvailable()) {
            return 'Permission denied: your account is not allowed to use this tool.';
        }

        try {
            return $this->run($request);
        } catch (ValidationException $exception) {
            return 'Invalid arguments: '.implode(' ', $exception->validator->errors()->all());
        } catch (ModelNotFoundException) {
            return 'Not found: no record matches the given id.';
        }
    }

    /**
     * Encode a tool result for the model.
     *
     * @param  array<array-key, mixed>  $data
     */
    protected function json(array $data): string
    {
        return (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
