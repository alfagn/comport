<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssistantApprovalRequest;
use App\Http\Requests\Admin\AssistantPromptRequest;
use App\Services\DashboardAssistantService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Empetix Command Copilot endpoints: full workspace page + JSON endpoints
 * shared with the slide-over Copilot drawer across all admin screens.
 */
class AssistantController extends Controller
{
    public function __construct(private DashboardAssistantService $assistant) {}

    /**
     * Render the full Dashboard AI Copilot workspace.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $conversations = $this->assistant->listConversations($user, 30);
        $requestedId = $request->query('conversation');

        $activeConversation = null;
        if (is_string($requestedId) && $requestedId !== '') {
            $activeConversation = $this->assistant->getConversation($user, $requestedId);
        } elseif ($conversations->isNotEmpty()) {
            $activeConversation = $this->assistant->getConversation($user, $conversations->first()['id']);
        }

        return view('admin.assistant.index', [
            'diagnostics' => $this->assistant->diagnostics($user),
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
        ]);
    }

    /**
     * Recent conversations list for the global Copilot drawer.
     */
    public function conversations(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'ok' => true,
            'diagnostics' => $this->assistant->diagnostics($user),
            'conversations' => $this->assistant->listConversations($user, 15),
        ]);
    }

    /**
     * Load one conversation's messages and pending approvals.
     */
    public function show(Request $request, string $conversation): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'conversation' => $this->assistant->getConversation($request->user(), $conversation),
        ]);
    }

    /**
     * Send a prompt to the Dashboard Copilot.
     */
    public function message(AssistantPromptRequest $request): JsonResponse
    {
        @set_time_limit(120);

        $user = $request->user();
        $rateKey = 'ai-dashboard:'.$user->id;
        $perMinute = (int) config('assistant.dashboard.per_minute', 20);

        if (RateLimiter::tooManyAttempts($rateKey, $perMinute)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return response()->json([
                'ok' => false,
                'message' => "Rate limit reached. Please wait {$seconds} seconds before sending another prompt.",
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        try {
            $conversation = $this->assistant->sendMessage(
                $user,
                (string) $request->validated('prompt'),
                $request->validated('conversation_id'),
            );
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (RuntimeException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'conversation' => $conversation,
            'conversations' => $this->assistant->listConversations($user, 20),
        ]);
    }

    /**
     * Approve or reject pending tool executions on a paused conversation.
     */
    public function approve(AssistantApprovalRequest $request, string $conversation): JsonResponse
    {
        @set_time_limit(120);

        $user = $request->user();

        try {
            $updated = $this->assistant->resolveApprovals(
                $user,
                $conversation,
                $request->validated('decisions'),
            );
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (RuntimeException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'conversation' => $updated,
        ]);
    }

    /**
     * Delete a conversation belonging to the current admin.
     */
    public function destroy(Request $request, string $conversation): JsonResponse|RedirectResponse
    {
        $this->assistant->deleteConversation($request->user(), $conversation);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Conversation deleted.',
            ]);
        }

        return redirect()->route('admin.assistant.index')->with('success', 'Conversation deleted.');
    }
}
