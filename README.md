# SODIERP

Sistema ERP interno da **ONG SODIPROM**, desenvolvido em **Laravel 10** com **Laravel Breeze** (Blade + Alpine + Tailwind) para autenticação e front-end.

---

## 📌 Requisitos

- PHP >= 8.2 (usando PHP 8.3 no WAMP)  
- Composer >= 2.8  
- MySQL ou MariaDB  
- Node.js >= 18 + NPM  
- WAMP (ou outro servidor local)  

---

## ⚙️ Instalação

### 1. Clonar o repositório
```bash
cd C:\wamp64\www
git clone <url-do-repositorio> sodierp
cd sodierp
2. Instalar dependências PHP
bash
Copiar código
composer install
3. Instalar dependências front-end
bash
Copiar código
npm install
4. Configurar variáveis de ambiente
Copie o arquivo .env.example para .env e configure:

env
Copiar código
APP_NAME=SODIERP
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://sodierp.local

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sodierp
DB_USERNAME=root
DB_PASSWORD=
5. Gerar chave da aplicação
bash
Copiar código
php artisan key:generate
6. Rodar as migrations
bash
Copiar código
php artisan migrate
7. Compilar os assets do front-end
bash
Copiar código
npm run dev
🚀 Executando a aplicação
Usando Artisan:

bash
Copiar código
php artisan serve
Acesse: http://127.0.0.1:8000

Usando WAMP + Virtual Host:
Configure um Virtual Host apontando para:

vbnet
Copiar código
C:\wamp64\www\sodierp\public
E acesse pelo navegador: http://sodierp.local

📌 Status do Projeto
✅ Etapa 1 – Base do Projeto
Projeto Laravel 10 criado ✔️

Configuração do .env e banco ✔️

Migrations iniciais rodadas ✔️

Laravel Breeze configurado ✔️

✅ Etapa 2 – Estrutura de Acesso
Roles criadas: admin, coord ✔️

Relação User ↔ Role configurada ✔️

Middleware CheckRole criado e registrado ✔️

Teste de criação de usuários com roles ✔️

Pendentes:

Policies detalhadas por módulo ❌

🔹 Etapa 3 – Módulos Principais
Controllers implementados:
Controller	Status	Middleware
AdminController	✔️	role:admin
CoordController	✔️	role:coord
ProgramaController	✔️	role:coord
TurmaController	✔️	role:coord
JovemController	✔️	role:admin,coord
ProfileController	✔️	auth

Controllers pendentes:
Controller	Status	Middleware
AvaliacaoController	❌	role:coord
CertificadoController	❌	role:admin,coord
OcorrenciaController	❌	role:coord
AgendaPsicologicaController	❌	role:coord

Rotas de teste criadas:
/admin/dashboard → acessível apenas por admin ✔️

/coord/dashboard → acessível apenas por coord ✔️

🔹 Etapa 4 – Recursos Extras
Exportar relatórios em PDF ❌

Exclusão automática de dados inativos (LGPD) ❌

Sistema de notificações internas ❌

Dashboard com estatísticas ❌

🔹 Etapa 5 – Qualidade e Deploy
Testes unitários e de integração (PHPUnit) ❌

Preparar ambiente de produção (servidor/VPS) ❌

Documentar APIs (se necessário) ❌

Versão final para uso da ONG ❌

Autor
SODIPROM - Desenvolvimento interno do sistema SODIERP.

yaml
Copiar código

---

Se você quiser, posso **fazer também uma versão visual em checklist para o GitHub**, onde cada tarefa concluída ou pendente aparece como checkbox, para facilitar acompanhar o progresso diariamente.  

Quer que eu faça isso também?

💻 Guia de Comandos Úteis
Artisan
Comando	Função
php artisan migrate	Executa todas as migrations pendentes
php artisan migrate:fresh	Apaga todas as tabelas e recria as migrations
php artisan key:generate	Gera a chave de criptografia da aplicação
php artisan make:model NomeDoModelo	Cria um model
php artisan make:migration nome_da_migration	Cria uma migration
php artisan serve	Inicializa servidor local Laravel
php artisan tinker	Abre console interativo para testar modelos e queries

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
⚠️ Observação: Sempre digite cada bloco no Tinker linha por linha; ele não aceita colar múltiplas linhas de uma vez.

Autor
SODIPROM - Desenvolvimento interno do sistema SODIERP.