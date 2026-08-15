<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BootstrapPlatform extends Command
{
    protected $signature = 'platform:bootstrap';

    protected $description = 'Configura o administrador global e a primeira empresa da plataforma';

    public function handle(): int
    {
        $this->newLine();
        $this->info('InfoGate Gestão - Configuração inicial');
        $this->newLine();

        $name = trim((string) $this->ask(
            'Nome do administrador global',
            'Administrador InfoGate'
        ));

        $email = strtolower(trim((string) $this->ask(
            'E-mail do administrador global'
        )));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('E-mail inválido.');

            return self::FAILURE;
        }

        $password = (string) $this->secret(
            'Senha do administrador global (mínimo 10 caracteres)'
        );

        if (strlen($password) < 10) {
            $this->error('A senha deve possuir pelo menos 10 caracteres.');

            return self::FAILURE;
        }

        $confirmation = (string) $this->secret(
            'Confirme a senha'
        );

        if ($password !== $confirmation) {
            $this->error('As senhas não coincidem.');

            return self::FAILURE;
        }

        [$user, $company, $branch] = DB::transaction(
            function () use ($name, $email, $password) {

                $user = User::firstOrNew([
                    'email' => $email,
                ]);

                $user->name = $name;
                $user->password = $password;
                $user->is_super_admin = true;
                $user->email_verified_at = $user->email_verified_at ?? now();
                $user->save();

                $company = Company::firstOrCreate(
                    [
                        'slug' => 'canal-som',
                    ],
                    [
                        'name' => 'Canal Som',
                        'trade_name' => 'Canal Som',
                        'plan' => 'pilot',
                        'status' => 'active',
                        'timezone' => 'America/Sao_Paulo',
                        'locale' => 'pt_BR',
                        'currency' => 'BRL',
                    ]
                );

                $branch = Branch::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => 'Matriz',
                    ],
                    [
                        'code' => 'MATRIZ',
                        'is_main' => true,
                        'status' => 'active',
                    ]
                );

                $company->users()->syncWithoutDetaching([
                    $user->id => [
                        'role' => 'owner',
                        'is_active' => true,
                    ],
                ]);

                $branch->users()->syncWithoutDetaching([
                    $user->id => [
                        'is_active' => true,
                    ],
                ]);

                return [$user, $company, $branch];
            }
        );

        $this->newLine();
        $this->info('Configuração concluída com sucesso.');
        $this->newLine();

        $this->table(
            ['Registro', 'ID', 'Nome'],
            [
                ['Administrador global', $user->id, $user->name],
                ['Empresa piloto', $company->id, $company->trade_name],
                ['Filial', $branch->id, $branch->name],
            ]
        );

        $this->newLine();
        $this->line('A senha não foi armazenada em texto puro.');
        $this->newLine();

        return self::SUCCESS;
    }
}
