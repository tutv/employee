<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'departments', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->string( 'name' );
			$table->integer( 'manager_id' )->nullable();
			$table->string( 'phone' );
			$table->integer( 'cover_id' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'departments' );
	}
}
