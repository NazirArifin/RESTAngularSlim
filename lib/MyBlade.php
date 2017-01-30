<?php
namespace Lib;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Factory;
use Philo\Blade\Blade as Blade;

class MyBlade extends Blade {
  /**
   * Register the view environment.
   *
   * @return void
   */
  public function registerFactory()
  {
    // Next we need to grab the engine resolver instance that will be used by the
    // environment. The resolver will be used by an environment to get each of
    // the various engine implementations such as plain PHP or Blade engine.
    $resolver = $this->container['view.engine.resolver'];

    $finder = $this->container['view.finder'];

    $env = new Factory($resolver, $finder, $this->container['events']);

    // We will also set the container instance on this view environment since the
    // view composers may be classes registered in the container, which allows
    // for great testable, flexible composers for the application developer.
    $env->setContainer($this->container);
    $env->addExtension('html', 'blade');

    return $env;
  }
}
