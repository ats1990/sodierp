    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('presencas', function (Blueprint $table) {
                $table->id();

                // Chaves Estrangeiras
                $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
                $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
                // Assume que o professor é um 'User'
                $table->foreignId('professor_id')->nullable()->constrained('users')->onDelete('set null'); 
                
                // Dados da Presença
                $table->date('data');
                $table->boolean('presente')->default(false); // true=Presente, false=Falta
                
                $table->timestamps();

                // Garante que um aluno só possa ter um registro de presença por dia em uma turma
                $table->unique(['aluno_id', 'turma_id', 'data']);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('presencas');
        }
    };
    
