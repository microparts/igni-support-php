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
use LogicException;
use Tmconsulting\Configuration;

class ServiceInfoModule implements ControllerProvider
{
    /**
     * @param HttpApplication|\Igni\Application\ControllerAggregator $controllers
     */
    public function provideControllers(ControllerAggregator $controllers): void
    {
        /** @var Configuration $conf */
        $conf = $controllers->getContainer()->get(Configuration::class);

        if (! $service = $conf->get('service', false)) {
            // https://confluence.teamc.io/pages/viewpage.action?pageId=7668210
            throw new LogicException('Please provide service info configuration in the ./configuration/defaults folder.');
        }

        $controllers->register(function () use ($service) {
            return Response::asJson([
                'service' => $service,
                'message' => 'hello stranger!',
            ]);
        }, Route::get('/'));
    }
}
