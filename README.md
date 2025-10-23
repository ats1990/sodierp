# 🏢 SODIPROM ERP

Sistema ERP interno da **ONG SODIPROM**, desenvolvido em **Laravel 10** com a pilha **TALL** (**T**ailwind, **A**lpine, **L**aravel, **L**ivewire) e **Laravel Breeze** (usando **Blade** como motor de *templates*).

---

## 📌 Requisitos

Certifique-se de que os seguintes requisitos estão instalados em seu ambiente local:

- [x] PHP >= 8.2 (Recomendado: PHP 8.3)
- [x] Composer >= 2.8
- [x] MySQL ou MariaDB
- [x] Node.js >= 18 + NPM
- [x] WAMP (ou outro servidor local, como XAMPP ou Laragon)

---

## ⚙️ Instalação

O projeto utiliza o **Blade** como base para as *views* e o **Livewire** para adicionar dinamismo à interface (edição *in-line*, etc.). O Laravel Breeze configura o Tailwind e o Alpine.

**Atenção:** É crucial instalar o Livewire com `composer require` e, em seguida, as dependências JavaScript (`npm install`).

| Passo | Comando | Descrição |
| :--- | :--- | :--- |
| **1. Clonar/Acessar** | `cd C:\wamp64\www\sodierp` | Acessa o diretório do projeto. |
| **2. Dependências PHP** | `composer install` | Instala as dependências base do **Laravel** e do **Blade** (via Breeze). |
| **3. Adicionar Livewire** | `composer require livewire/livewire` | Instala o framework **Livewire**. |
| **4. Dependências JS** | `npm install` | Instala **Tailwind** e **Alpine** e outras dependências JS. |
| **5. Configuração** | `cp .env.example .env` | Cria o arquivo de ambiente. |
| **6. Chave e DB** | `php artisan key:generate`<br>`php artisan migrate` | Gera a chave de segurança e cria as tabelas no banco de dados. |
| **7. Compilar Assets** | `npm run dev` | Compila os arquivos CSS/JS. |
| **8. Servidor** | `php artisan serve` | Inicializa o servidor local do Laravel. |

Acesse: `http://127.0.0.1:8000` (ou o seu Virtual Host).

---

## 📌 Status do Projeto

### ✅ Etapa 1 – Base do Projeto
- [x] Projeto Laravel 10 criado
- [x] Configuração do `.env` e banco
- [x] Migrations iniciais rodadas
- [x] Laravel Breeze configurado

### ✅ Etapa 2 – Estrutura de Acesso
- [x] Roles criadas: `admin`, `coord`
- [x] Relação User ↔ Role configurada
- [x] Middleware `CheckRole` criado e registrado
- [x] Teste de criação de usuários com roles
- [ ] Policies detalhadas por módulo

### 🔹 Etapa 3 – Módulos Principais
**Controllers implementados:**
- [x] `AdminController` – `role:admin`
- [x] `CoordController` – `role:coord`
- [x] `ProgramaController` – `role:coord`
- [x] `TurmaController` – `role:coord`
- [x] `JovemController` – `role:admin`, `coord`
- [x] `ProfileController` – `auth`

**Controllers pendentes:**
- [ ] `AvaliacaoController` – `role:coord`
- [ ] `CertificadoController` – `role:admin`, `coord`
- [ ] `OcorrenciaController` – `role:coord`
- [ ] `AgendaPsicologicaController` – `role:coord`

**Rotas de teste criadas:**
- [x] `/admin/dashboard` → admin
- [x] `/coord/dashboard` → coord

### ✅ Etapa 4 – Gerenciamento de Usuários (Módulo Coordenação)
- [x] Lógica de listagem e filtro de usuários
- [x] Funcionalidade de **Ativar/Desativar** status do usuário
- [x] Implementação com **Livewire** para edição de dados **in-line** (Nome Completo, E-mail, Tipo)
- [x] Restrição para Coordenador não desativar a própria conta

### 🔹 Etapa 5 – Views / Front-end (Pendentes)
**Views criadas / ajustadas:**
- [x] `login.blade.php` – logo e cores ajustadas
- [x] `layouts/guest.blade.php` – componente `x-guest-layout`
- [x] Partials de input, errors e botões (`x-input-label`, `x-text-input`, `x-primary-button`)
- [x] Componente Livewire `GerenciarUsuarios` (`livewire/gerenciar-usuarios.blade.php`)

**Views a criar / melhorar:**
- [ ] Dashboard do Admin (`admin/dashboard.blade.php`)
- [ ] Dashboard do Coord (`coord/dashboard.blade.php`)
- [ ] Formulários de Programas, Turmas e Jovens (`programa/*.blade.php`, `turma/*.blade.php`, `jovem/*.blade.php`)
- [ ] Listagem e detalhes de Avaliações (`avaliacao/*.blade.php`)
- [ ] Certificados (`certificado/*.blade.php`)
- [ ] Ocorrências (`ocorrencia/*.blade.php`)
- [ ] Agenda Psicológica (`agenda_psicologica/*.blade.php`)
- [ ] Relatórios PDF (layout)

### 🔹 Etapa 6 – Recursos Extras
- [ ] Exportar relatórios em PDF
- [ ] Exclusão automática de dados inativos (LGPD)
- [ ] Sistema de notificações internas
- [ ] Dashboard com estatísticas

### 🔹 Etapa 7 – Qualidade e Deploy
- [ ] Testes unitários e de integração (PHPUnit)
- [ ] Preparar ambiente de produção (servidor/VPS)
- [ ] Documentar APIs (se necessário)
- [ ] Versão final para uso da ONG

---

## 💻 Guia de Comandos Úteis

### Artisan

| Comando | Descrição |
| :--- | :--- |
| `php artisan migrate` | Executa todas as migrations pendentes. |
| `php artisan migrate:fresh` | **CUIDADO!** Apaga todas as tabelas e recria as migrations. |
| `php artisan make:model NomeDoModelo` | Cria um model. |
| `php artisan make:migration nome_da_migration` | Cria uma migration. |
| `php artisan make:livewire NomeDoComponente` | Cria um componente **Livewire**. |
| `php artisan serve` | Inicializa servidor local Laravel. |
| `php artisan tinker` | Abre console interativo. |

### Tinker (Exemplos de Criação de Dados)

Use o console `php artisan tinker` para criar dados de teste no banco:

```php
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
