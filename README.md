Sistema ERP interno da **ONG SODIPROM**, desenvolvido em **Laravel 10** com **Laravel Breeze** (Blade + Alpine + Tailwind).

---

## 📌 Requisitos

- [x] PHP >= 8.2 (usando PHP 8.3 no WAMP)  
- [x] Composer >= 2.8  
- [x] MySQL ou MariaDB  
- [x] Node.js >= 18 + NPM  
- [x] WAMP (ou outro servidor local)  

---

## ⚙️ Instalação

```bash
cd C:\wamp64\www
git clone <url-do-repositorio> sodierp
cd sodierp
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
Acesse: http://127.0.0.1:8000 ou configure Virtual Host para http://sodierp.local.

📌 Status do Projeto
✅ Etapa 1 – Base do Projeto
 Projeto Laravel 10 criado

 Configuração do .env e banco

 Migrations iniciais rodadas

 Laravel Breeze configurado

✅ Etapa 2 – Estrutura de Acesso
 Roles criadas: admin, coord

 Relação User ↔ Role configurada

 Middleware CheckRole criado e registrado

 Teste de criação de usuários com roles

Pendentes:

 Policies detalhadas por módulo

🔹 Etapa 3 – Módulos Principais
Controllers implementados:

 AdminController – role:admin

 CoordController – role:coord

 ProgramaController – role:coord

 TurmaController – role:coord

 JovemController – role:admin,coord

 ProfileController – auth

Controllers pendentes:

 AvaliacaoController – role:coord

 CertificadoController – role:admin,coord

 OcorrenciaController – role:coord

 AgendaPsicologicaController – role:coord

Rotas de teste criadas:

 /admin/dashboard → admin

 /coord/dashboard → coord

🔹 Etapa 4 – Views / Front-end
Views criadas / ajustadas:

 login.blade.php – logo e cores ajustadas

 layouts/guest.blade.php – componente x-guest-layout

 Partials de input, errors e botões (x-input-label, x-text-input, x-primary-button)

Views a criar / melhorar:

 Dashboard do Admin (admin/dashboard.blade.php)

 Dashboard do Coord (coord/dashboard.blade.php)

 Formulários de Programas, Turmas e Jovens (programa/*.blade.php, turma/*.blade.php, jovem/*.blade.php)

 Listagem e detalhes de Avaliações (avaliacao/*.blade.php)

 Certificados (certificado/*.blade.php)

 Ocorrências (ocorrencia/*.blade.php)

 Agenda Psicológica (agenda_psicologica/*.blade.php)

 Relatórios PDF (layout)

🔹 Etapa 5 – Recursos Extras
 Exportar relatórios em PDF

 Exclusão automática de dados inativos (LGPD)

 Sistema de notificações internas

 Dashboard com estatísticas

🔹 Etapa 6 – Qualidade e Deploy
 Testes unitários e de integração (PHPUnit)

 Preparar ambiente de produção (servidor/VPS)

 Documentar APIs (se necessário)

 Versão final para uso da ONG

💻 Guia de Comandos Úteis
Artisan

 php artisan migrate – Executa todas as migrations pendentes

 php artisan migrate:fresh – Apaga todas as tabelas e recria as migrations

 php artisan key:generate – Gera a chave de criptografia da aplicação

 php artisan make:model NomeDoModelo – Cria um model

 php artisan make:migration nome_da_migration – Cria uma migration

 php artisan serve – Inicializa servidor local Laravel

 php artisan tinker – Abre console interativo para testar modelos e queries

Tinker (exemplos)

php
Copiar código
// Importar models
use App\Models\User;
use App\Models\Role;

// Criar roles
Role::create(['name' => 'admin']);
Role::create(['name' => 'coord']);

// Criar usuários
User::create([
    'name' => 'Administrador',
    'email' => 'admin@sodiprom.org',
    'password' => 'senha123',
    'role_id' => 1,
]);
User::create([
    'name' => 'Coordenador',
    'email' => 'coord@sodiprom.org',
    'password' => 'senha123',
    'role_id' => 2,
]);

// Listar usuários
User::all();

// Listar roles
Role::all();