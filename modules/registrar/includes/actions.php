<?php
/**
 * SMS 2 - Registrar Secure Action Dispatcher
 *
 * Reusable gateway for authenticated, authorized, CSRF-protected JSON actions.
 *
 * Handlers are registered as: actionSlug => callable(string $action): array
 * The callable returns extra payload fields; regHandleActions() responds with a
 * 200 {ok:true, ...} JSON payload. Handlers may also respond directly with
 * regOk()/regFail() for full control.
 *
 * Method enforcement: POST only.
 * Security chain: method check -> CSRF token check (403) -> auth (401) -> role (403).
 */
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (!function_exists('regHandleActions')) {
    function regHandleActions(array $handlers): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            regHttpError(405, 'Method not allowed.');
        }

        $action = $_POST['reg_action'] ?? '';
        if (!is_string($action) || $action === '' || !isset($handlers[$action])) {
            regHttpError(400, 'Unknown or missing action.');
        }

        regRequireCsrf();
        regRequireAction($action);

        $result = call_user_func($handlers[$action], $action);
        regOk(is_array($result) ? $result : []);
    }
}