<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 26/10/2018
 */

namespace Microparts\Igni\Support\Request;

use Psr\Http\Message\ServerRequestInterface;

trait JsonDecodeTrait
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    protected function json(ServerRequestInterface $request): array
    {
        return (array) json_decode((string) $request->getBody(), true);
    }
}
