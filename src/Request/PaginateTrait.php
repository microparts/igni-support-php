<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 26/10/2018
 */

namespace Microparts\Igni\Support\Request;

use Microparts\PaginateFormatter\PaginateFormatter;
use Pagerfanta\Pagerfanta;
use Psr\Http\Message\ServerRequestInterface;

trait PaginateTrait
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param $adapter
     * @return array
     */
    protected function paginate(ServerRequestInterface $request, $adapter)
    {
        $query = $request->getQueryParams();

        $page = new Pagerfanta($adapter);
        $page->setCurrentPage((int) ($query['page'] ?? 1));
        $page->setMaxPerPage((int) ($query['per_page'] ?? 15));
        $paginate = new PaginateFormatter($page);

        return $paginate->format();
    }
}
