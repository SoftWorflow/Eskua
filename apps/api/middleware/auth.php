<?php
require_once(__DIR__ . '/../../backend/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {

    // Verifys that the user is authanticated
    public static function authenticate() : ?array {
        $headers = getallheaders();
        
        // Search for the token in the "Authorization" header
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(['error' => 'No authorization token provided', 'ok' => false]);
            exit;
        }

        $parts = explode(' ', $authHeader);
        
        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid authorization format', 'ok' => false]);
            exit;
        }

        $token = $parts[1];
        $secretKey = getenv('CLIENT_TOKEN_SECRET');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $payload = (array) $decoded;
            
            return $payload;
        } catch (\Firebase\JWT\ExpiredException $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token expired', 'ok' => false]);
            exit;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token', 'ok' => false]);
            exit;
        }
    }

    // Verifys that the user has the allowed Roles
    public static function authorize(array $allowedRoles) : array {
        $payload = self::authenticate();
        
        $userRole = $payload['role'] ?? null;
        
        if (!$userRole || !in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Access denied. Insufficient permissions.', 
                'ok' => false,
                'required_roles' => $allowedRoles,
                'your_role' => $userRole
            ]);
            exit;
        }

        return $payload;
    }

}
?>