<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GPTController as GPT;
use Discord\Discord;

class DiscordBot extends Command
{
    protected $gpt;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:run-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Discord bot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GPT $gpt)
    {
        $this->gpt = $gpt;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $discord = new Discord(['token' => env('DISCORD_BOT_TOKEN')]);

        $discord->on('ready', function ($discord) {
            echo "Bot is ready!", PHP_EOL;

            $discord->on('guildCreate', function ($guild) {
                echo "Joined a new guild: {$guild->name}", PHP_EOL;
            });

            $discord->on('guildUpdate', function ($oldGuild, $newGuild) {
                echo "Guild updated: {$oldGuild->name} to {$newGuild->name}", PHP_EOL;
            });

            // Listen for messages.
            $discord->on('message', function ($message) use ($discord) {
                $this->feliz($message, true);
                $this->chat($message, false);
            });
        });

        $discord->run();
    }

    function feliz ($message, $deleteCommandMessage = false): void
    {
        $command = "!feliz";

        if (strpos($message->content, $command) === 0) {
            if ($deleteCommandMessage) $message->delete();

            echo $message->author->username . " usou o comando $command", PHP_EOL;

            try {
                $message->reply($this->gpt->getMotivationalMessage());
                sleep(1);
            } catch (\Exception $e) {
                echo "Erro ao enviar mensagem: " . $e->getMessage(), PHP_EOL;
                $message->reply("Erro ao enviar mensagem");
            }
        }
    }

    function chat ($message, $deleteCommandMessage = false): void
    {
        $command = "!chat";

        if (strpos($message->content, $command) === 0) {
            if ($deleteCommandMessage) $message->delete();

            echo $message->author->username . " usou o comando $command", PHP_EOL;

            try {
                $message->reply($this->gpt->getHelp($message));
            } catch (\Exception $e) {
                echo "Erro ao enviar mensagem: " . $e->getMessage(), PHP_EOL;
                $message->reply("Erro ao enviar mensagem");
            }
        }
    }
}
