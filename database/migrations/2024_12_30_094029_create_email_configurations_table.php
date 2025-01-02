<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailConfigurationsTable extends Migration
{
/**
* Run the migrations.
*
* @return void
*/
public function up()
{
Schema::create('email_configurations', function (Blueprint $table) {
$table->id();
$table->unsignedBigInteger('company_id'); // Relación con la tabla de empresas
$table->string('smtp_host');
$table->string('smtp_port');
$table->string('smtp_username');
$table->string('smtp_password'); // Asegúrate de cifrarla en tu código
$table->string('smtp_encryption')->nullable(); // Opcional: TLS, SSL, etc.
$table->string('from_email');
$table->string('from_name');
$table->timestamps();

// Llave foránea para la relación con empresas
$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
});
}

/**
* Reverse the migrations.
*
* @return void
*/
public function down()
{
Schema::dropIfExists('email_configurations');
}
}
