<?php

namespace Didix16\LaraPrime\Commands;

use Didix16\LaraPrime\LaraPrimeServiceProvider;
use Illuminate\Console\Command;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'laraprime:dev')]
class DevCommand extends Command
{
    /**
     * The console command signature.
     * @var string
     */
    protected  $signature = 'laraprime:dev {--force : Overwrite LaraPrimeProvider to original state and apply dev patches}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Prepare LaraPrime for development environment';

    protected string $providerContent = '';

    public function __construct(protected ParserFactory $parserFactory)
    {

        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle(): void
    {
        if ($this->confirm('So you are a developer👩‍💻, huh? And wanna improve LaraPrime?')) {
            $this->comment('Great! Let me help you with that...');
            $this->warn('Remember that this Laravel app is only for development🔬 purposes!');

            $force = $this->option('force');
            if($force){
                $this->info('Forcing LaraPrimeProvider modification...');
                $this
                    ->executeCommand('vendor:publish', [
                        '--provider' => LaraPrimeServiceProvider::class,
                        '--tag'      => [
                            'laraprime-app-stubs',
                        ],
                        '--force' => true
                    ]);

            }

            // Check if we have to modify LaraPrimeProvider
            $this->info('Checking LaraPrimeProvider...');
            if(!file_exists(app_path('LaraPrime/LaraPrimeProvider.php'))){
                $this->error('LaraPrimeProvider not found ❌');
                return;
            }

            $this->providerContent = file_get_contents(app_path('LaraPrime/LaraPrimeProvider.php'));

            if (
                !$force &&
                str_contains($this->providerContent, 'Didix16\LaraPrime\Decorators\ViteDecorator') &&
                str_contains($this->providerContent, 'Illuminate\Foundation\Vite') &&
                str_contains($this->providerContent, '$this->app->bind(Vite::class, function ($app) {') &&
                str_contains($this->providerContent, 'if (is_file(public_path(\'vendor/laraprime/hot\'))) {') &&
                str_contains($this->providerContent, 'return new ViteDecorator(new Vite());')

            ) {
                $this->info('LaraPrimeProvider seems already modified ✅');
                $this->showReminder();
                return;
            }

            // Modify LaraPrimeProvider register method
            $this->info('Modifying LaraPrimeProvider...🧪');
            $this->modifyLaraPrimeProvider();

        }
    }

    protected function modifyLaraPrimeProvider(): void
    {
        $providerPath = app_path('LaraPrime/LaraPrimeProvider.php');

        $parser = $this->parserFactory->createForNewestSupportedVersion();
        try {
            $ast = $parser->parse($this->providerContent);
            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($this) extends NodeVisitorAbstract {

                public function __construct(protected DevCommand $command)
                {
                }

                public function leaveNode(Node $node)
                {
                    if($node instanceof Node\Stmt\Use_ && $node->uses[0]->name->name === 'Didix16\LaraPrime\LaraPrimeAppServiceProvider'){
                        return [
                            $node,
                            new Node\Stmt\Use_([new Node\UseItem(new Node\Name('Didix16\LaraPrime\Decorators\ViteDecorator'))]),
                            new Node\Stmt\Use_([new Node\UseItem(new Node\Name('Illuminate\Foundation\Vite'))])
                        ];
                    }
                    return null;
                }

                public function enterNode(Node $node)
                {

                    if($node instanceof Node\Stmt\ClassMethod && $node->name->name === 'register'){

                        $code = <<<'CODE'
<?php

if (is_file(public_path('vendor/laraprime/hot'))) {
    $this->app->bind(Vite::class, function ($app) {
        return new ViteDecorator(new Vite());
    });
}
CODE;

                        $parser = (new ParserFactory())->createForNewestSupportedVersion();
                        try {
                            $ast = $parser->parse($code);
                        } catch (Error $error) {
                            $this->command->error(sprintf('Error parsing LaraPrimeProvider file: %s', $error->getMessage()));
                            return;
                        }

                        $node->stmts = array_merge($node->stmts, $ast);
                    }
                }
            });

            $ast = $traverser->traverse($ast);

            $prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
            $writed = file_put_contents($providerPath, $prettyPrinter->prettyPrintFile($ast));
            if($writed){
                $this->info('LaraPrimeProvider modified successfully! ✅');
                $this->showReminder();

            }else{
                $this->error('Error modifying LaraPrimeProvider ❌');
            }
        }catch (Error $error){
            $this->error(sprintf('Error parsing LaraPrimeProvider file: %s', $error->getMessage()));
        }

    }

    protected function showReminder(): self
    {
        $this->info('Now you can run 💻 `yarn dev` on LaraPrime package to start the development server');
        $this->info('And run 💻 `php artisan serve` on this Laravel app to start the embedded server');
        $this->info('Go to ➡️ http://localhost:8000/admin to see the LaraPrime dashboard/login page');
        $this->info('Happy coding!? ⭐💫');

        return $this;
    }

    private function executeCommand(string $command, array $parameters = []): self
    {
        try {
            $result = $this->callSilent($command, $parameters);
        } catch (\Exception $exception) {
            $result = 1;
            $this->alert($exception->getMessage());
        }

        if ($result) {
            $parameters = http_build_query($parameters, '', ' ');
            $parameters = str_replace('%5C', '/', $parameters);
            $this->alert("An error has occurred. The '{$command} {$parameters}' command was not executed");
        }

        return $this;
    }
}
