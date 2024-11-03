<?php

namespace Swiftyper\fbt\Console\Commands;

use Illuminate\Console\Command;
use Swiftyper\Exception\ApiErrorException;
use Swiftyper\Fbt;
use Swiftyper\Phrase;
use Swiftyper\Swiftyper;
use Swiftyper\Translation;

class SwiftyperFbtCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swiftyper:fbt {--deploy : Deploy reviewed app translations}' . PHP_EOL .
                           '              {--upload= : Upload phrases/translations to swiftyper}' . PHP_EOL .
                           '              {--init : Connect fbt project with swiftyper}' . PHP_EOL .
                           '              {--pretty=true : Pretty print output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync translations/native phrases with swiftyper.';

    /**
     * Cache storage path for generated translations & source strings.
     *
     * @var string
     */
    private $fbtDir;

    public function handle(): int
    {
        $this->comment(
            <<<LOGO
  ___        _  __ _                        ___ ___ _____ 
 / __|_ __ _(_)/ _| |_ _  _ _ __  ___ _ _  | __| _ )_   _|
 \__ \ V  V / |  _|  _| || | '_ \/ -_) '_| | _|| _ \ | |  
 |___/\_/\_/|_|_|  \__|\_, | .__/\___|_|   |_| |___/ |_|  
                       |__/|_|
LOGO
        );

        $this->fbtDir = \config('fbt.path') . '/';

        if (! is_dir($this->fbtDir)) {
            mkdir($this->fbtDir, 0755, true);
        }

        Swiftyper::setApiKey(\config('swiftyper.api_key'));

        try {
            if ($this->option('init')) {
                $this->init();
            } elseif ($this->option('deploy')) {
                $this->deploy();
            } elseif ($this->hasOption('upload')) {
                $this->upload((string) $this->option('upload'));
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @throws ApiErrorException
     */
    private function init()
    {
        $this->info('🚀  Initializing project...');

        Fbt::initialize([
            'platform' => 'php',
            'version' => \Composer\InstalledVersions::getVersion('richarddobron/fbt'),
            'hash_module' => \config('fbt.hash_module'),
            'md5_digest' => \config('fbt.md5_digest'),
        ]);

        $this->warn('Project is prepared.');

        if ($this->confirm('Do want to upload current phrases/translations?')) {
            $this->call('swiftyper:fbt', [
                '--upload' => true,
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function upload(string $path)
    {
        $this->info('⚡  Uploading phrases...');

        $phrases = $path ?: $this->fbtDir . '/.source_strings.json';

        if (! file_exists($phrases)) {
            $this->warn('Native phrases file (' . $phrases . ') not found!');
        } else {
            $swiftyper = Phrase::upload([
                'native_strings' => file_get_contents($phrases),
            ]);

            $this->info($swiftyper->saved . ' phrases has been stored.');
        }

        $translations = $this->fbtDir . '/.translations.json';

        if (! file_exists($translations)) {
            $this->warn('Translations file (' . $translations . ') not found!');
        } else {
            $trans = file_get_contents($translations);
            if (! json_decode($trans)) {
                $this->error('Translations file is empty.');
            } else {
                $swiftyper = Translation::upload([
                    'translations' => $trans,
                ]);

                $this->info($swiftyper->translations . ' translations has been stored.');
            }
        }
    }

    /**
     * @throws ApiErrorException
     * @throws \Exception
     */
    private function deploy()
    {
        $this->info('👽  Translating app...');

        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if ($this->option('pretty')) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $file = $this->fbtDir . '/translatedFbts.json';

        if (! is_dir($this->fbtDir) || ! is_writable($this->fbtDir)) {
            throw new \Exception("Directory $this->fbtDir is not writable.");
        } elseif (is_file($file) && ! is_writable($file)) {
            throw new \Exception("File $file is not writable.");
        }

        $translations = Translation::raw([
            'fallback' => \config('fbt.fallback', []),
        ]);
        file_put_contents($file, json_encode($translations, $flags));

        $this->info('Translations has been deployed.');
    }
}
