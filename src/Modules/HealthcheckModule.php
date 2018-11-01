<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 01/11/2018
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\ControllerAggregator;
use Igni\Application\HttpApplication;
use Igni\Application\Providers\ControllerProvider;
use Igni\Network\Http\Response;
use Igni\Network\Http\Route;

class HealthcheckModule implements ControllerProvider
{
    /**
     * @param HttpApplication|\Igni\Application\ControllerAggregator $controllers
     */
    public function provideControllers(ControllerAggregator $controllers): void
    {
        $controllers->register(function () {
            return Response::asText('ok');
        }, Route::get('/healthcheck'));
    }
}
