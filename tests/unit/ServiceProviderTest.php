<?php

use Illuminate\Support\Facades\Artisan;
use Xinax\LaravelGettext\Facades\LaravelGettext as LaravelGettextFacade;
use Xinax\LaravelGettext\LaravelGettext;
use Xinax\LaravelGettext\Testing\BaseTestCase;

class ServiceProviderTest extends BaseTestCase
{
    public function testServiceIsRegistered()
    {
        $this->assertInstanceOf(LaravelGettext::class, $this->app->make('laravel-gettext'));
        $this->assertInstanceOf(LaravelGettext::class, $this->app->make(LaravelGettext::class));
        $this->assertSame($this->app->make('laravel-gettext'), $this->app->make(LaravelGettext::class));
    }

    public function testDefaultConfigIsMerged()
    {
        $this->assertSame('symfony', config('laravel-gettext.handler'));
        $this->assertSame('en_US', config('laravel-gettext.locale'));
    }

    public function testFacadeResolvesLocale()
    {
        $this->assertSame('en_US', LaravelGettextFacade::getLocale());
        $this->assertSame('en', LaravelGettextFacade::getLocaleLanguage());
        $this->assertTrue(LaravelGettextFacade::isLocaleSupported('en_US'));
    }

    public function testCommandsAreRegistered()
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('gettext:create', $commands);
        $this->assertArrayHasKey('gettext:update', $commands);
    }

    public function testPluralHelper()
    {
        $this->assertSame('2 apples', _n('%d apple', '%d apples', 2, 2));
        $this->assertSame('1 apple', _n('%d apple', '%d apples', 1, [1]));
    }
}
