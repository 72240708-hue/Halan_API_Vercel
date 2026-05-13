<?php


namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RoleMiddleware implements Middleware
{

    private string $requiredRole;

    public function __construct(string $role)
    {
        $this->requiredRole = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        //ambil payload jwt yang sudah di decode di JwtAuthentication middleware 
        $token = $request->getAttribute('token');

    //ekstrak role dari token (menangani array maupun object)
    $userRole = is_array($token) ? ($token['role'] ?? '') : ($token->role ?? '');

        if ($userRole !== $this->requiredRole) {
            // Jika role tidak sesuai, kembalikan response 403 Forbidden
            $response = new \Slim\Psr7\Response(); 
            $response->getBody()->write(json_encode(['status' => "Error", 'message' => 'Forbidden: Anda tidak memiliki akses (Dibutuhkan role: ' . $this->requiredRole . ')']));
            
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }
        //lanjut ke process request berikutnya
        return $handler->handle($request);
    }
}
