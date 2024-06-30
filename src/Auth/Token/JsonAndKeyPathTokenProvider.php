<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

use Illuminate\Http\Request;

class JsonAndKeyPathTokenProvider implements TokenProviderInterface
{
    private array $keyPathComponents;

    public function __construct(string $keyPath)
    {
        $this->keyPathComponents = explode('.', $keyPath);
    }

    public function provideToken(Request $request): ?string
    {
        $json = $request->json()->all();
        foreach ($this->keyPathComponents as $keyPathComponent) {
            if (!is_array($json) || !array_key_exists($keyPathComponent, $json)) {
                return null;
            }
            $json = $json[$keyPathComponent];
        }
        return $json;
    }
}
