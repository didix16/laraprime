<?php

namespace Didix16\LaraPrime\UI\Page;

use Didix16\LaraPrime\Http\Controllers\Controller;
use Didix16\LaraPrime\Http\Requests\LaraPrimeRequest;
use Didix16\LaraPrime\UI\Metadata\Key;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

abstract class Page extends Controller
{
    /**
     * @param  mixed  ...$arguments
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     *
     * @see static::handle()
     */
    public function __invoke(LaraPrimeRequest $request, ...$arguments)
    {
        return $this->handle($request, ...$arguments);
    }

    /**
     * Build the layout tree that will be used to render the page.
     * This method must return an iterable with the layout components.
     */
    protected function buildLayout(): iterable
    {
        return [
            Key::LAYOUT() => [
                Key::NAME() => 'h1',
                Key::PROPS() => [

                    'className' => 'text-3xl font-bold underline',
                ],
                Key::CHILDREN() => [
                    "Test Title",
                    [
                        Key::NAME() => 'span',
                        Key::PROPS() => [],
                        Key::CHILDREN() => ['Test Span'],
                    ]
                ],
            ]
        ];
    }

    /**
     * Determine if the user is authorized and has the required rights to complete this request.
     */
    protected function checkAccess(LaraPrimeRequest $request): bool
    {
        // TODO: Implement the logic to check if the user has access to the page
        return true;

        $user = $request->user();

        if ($user === null) {
            return true;
        }
        // return $user->hasAnyAccess([]);
    }

    /**
     * Response or HTTP code that will be returned if user does not have access to the screen.
     *
     * @return int | \Symfony\Component\HttpFoundation\Response
     */
    public static function unaccessed()
    {
        return Response::HTTP_FORBIDDEN;
    }

    /**
     * Renders the page with the given parameters.
     */
    public function view(...$params)
    {
        return Inertia::render('Page', $this->buildLayout());
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \BadMethodCallException
     */
    public function handle(LaraPrimeRequest $request, ...$arguments)
    {

        $method = $request->route()->parameter('m', 'view');

        if (! $request->isMethodSafe()) {
            $method = Arr::last($request->route()->parameters(), null, 'view');
        }

        // $state = $this->extractState();
        // $this->fillPublicProperty($state);

        // Deny access without rights
        abort_unless($this->checkAccess($request), static::unaccessed());

        // Redirect for correct residual behavior
        if ($request->isMethodSafe() && $method !== 'view') {
            return redirect()->action([static::class], $request->all());
        }

        return $this->callMethod($method, $arguments) ?? throw new \BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }

    /**
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    private function callMethod(string $method, array $parameters = [])
    {
        $uses = static::class . '@' . $method;

        $preparedParameters = self::prepareForExecuteMethod($uses);

        return App::call($uses, $preparedParameters ?? $parameters);
    }

    /**
     * Prepare the method execution by binding route parameters and substituting implicit bindings.
     */
    public static function prepareForExecuteMethod(string $uses): ?array
    {
        $route = request()->route();

        if ($route === null) {
            return null;
        }

        collect(request()->query())->each(function ($value, string $key) use ($route) {
            $route->setParameter($key, $value);
        });

        $original = $route->action['uses'];

        $route = $route->uses($uses);

        Route::substituteImplicitBindings($route);

        $parameters = $route->parameters();

        $route->uses($original);

        return $parameters;
    }

    /**
     * Get the available methods that can be called on the page through the route request
     * via GET, HEAD and POST
     */
    public static function getAvailableMethods(): Collection
    {
        $class = (new \ReflectionClass(static::class))
            ->getMethods(\ReflectionMethod::IS_PUBLIC);

        return collect($class)
            ->mapWithKeys(fn(\ReflectionMethod $method) => [$method->name => $method])
            ->except(get_class_methods(Page::class))
            // ->except(['query'])
            /*
             * Route filtering requires at least one element to be present.
             * We set __invoke by default, since it must be public.
             */
            ->whenEmpty(fn() => collect('__invoke'))
            ->keys();
    }
}
