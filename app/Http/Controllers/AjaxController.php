<?php

namespace App\Http\Controllers;

use App\Department;
use App\Employee;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use stdClass;

class AjaxController extends Controller {
	/**
	 * Create new employee
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function addEmployee( Request $request ) {
		$input = $request->only( [
			'name',
			'email',
			'phone',
			'job',
			'department_id'
		] );

		$validator = Validator::make( $input, [
			'name'  => 'required',
			'phone' => 'required',
			'job'   => 'required',
			'email' => 'required|unique:employees|email',
		] );

		$response         = new stdClass();
		$response->return = true;
		$response->msg    = 'Creating successful new employee!';

		if ( $validator->fails() ) {
			$errors = $validator->errors()->getMessages();

			$response->return = false;
			$response->errors = $errors;

			$response->msg = 'Some fields are not valid!';

			return response()->json( $response );
		}

		$employee             = Employee::create( $input );
		$response->employee   = $employee;
		$response->http_refer = route( 'employee.show', $employee->id );

		return response()->json( $response );
	}

	/**
	 * Edit employee
	 *
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function editEmployee( $id, Request $request ) {

		$input = $request->only( [
			'name',
			'email',
			'phone',
			'job',
			'department_id'
		] );

		$response         = new stdClass();
		$response->return = true;
		$response->msg    = 'Updating successful employee!';

		$employee = Employee::find( $id );
		if ( ! $employee ) {
			$response->return = false;
			$response->msg    = 'Employee not found.';
			$response->errors = [ ];

			return response()->json( $response );
		}

		$validate_email = 'required|unique:employees|email';
		if ( $employee->email == $input['email'] ) {
			$validate_email = 'required|email';
		}

		$validator = Validator::make( $input, [
			'name'  => 'required',
			'phone' => 'required',
			'job'   => 'required',
			'email' => $validate_email,
		] );

		if ( $validator->fails() ) {
			$errors = $validator->errors()->getMessages();

			$response->return = false;
			$response->errors = $errors;

			$response->msg = 'Some fields are not valid!';

			return response()->json( $response );
		}

		$update = $employee->update( $input );
		if ( $update ) {
			$response->employee   = $employee;
			$response->http_refer = route( 'employee.show', $employee->id );
		} else {
			$response->return = false;
			$response->errors = [ ];
			$response->msg    = 'Some thing went wrong!';

			return response()->json( $response );
		}


		return response()->json( $response );
	}

	/**
	 * Create new department
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function addDepartment( Request $request ) {
		$input = $request->only( [
			'name',
			'phone',
			'manager_id'
		] );

		$validator = Validator::make( $input, [
			'name'  => 'required',
			'phone' => 'required',
		] );

		$response         = new stdClass();
		$response->return = true;
		$response->msg    = 'Creating successful new department!';

		if ( $validator->fails() ) {
			$errors = $validator->errors()->getMessages();

			$response->return = false;
			$response->errors = $errors;

			$response->msg = 'Some fields are not valid!';

			return response()->json( $response );
		}

		$department           = Department::create( $input );
		$response->employee   = $department;
		$response->http_refer = route( 'department.show', $department->id );

		return response()->json( $response );
	}

	/**
	 * Edit department
	 *
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function editDepartment( $id, Request $request ) {

		$input = $request->only( [
			'name',
			'phone',
			'manager_id'
		] );

		$response         = new stdClass();
		$response->return = true;
		$response->msg    = 'Updating successful department!';

		$department = Department::find( $id );
		if ( ! $department ) {
			$response->return = false;
			$response->msg    = 'Department not found.';
			$response->errors = [ ];

			return response()->json( $response );
		}


		$validator = Validator::make( $input, [
			'name'  => 'required',
			'phone' => 'required',
		] );

		if ( $validator->fails() ) {
			$errors = $validator->errors()->getMessages();

			$response->return = false;
			$response->errors = $errors;

			$response->msg = 'Some fields are not valid!';

			return response()->json( $response );
		}

		$update = $department->update( $input );
		if ( $update ) {
			$response->employee   = $department;
			$response->http_refer = route( 'department.show', $department->id );
		} else {
			$response->return = false;
			$response->errors = [ ];
			$response->msg    = 'Some thing went wrong!';

			return response()->json( $response );
		}

		return response()->json( $response );
	}

	/**
	 * Create new department
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function addUser( Request $request ) {
		$input = $request->only( [
			'name',
			'email',
		] );

		$validator = Validator::make( $input, [
			'name'  => 'required',
			'email' => 'required|unique:users|email',
		] );

		$response         = new stdClass();
		$response->return = true;
		$response->msg    = 'Creating successful new user!';

		if ( $validator->fails() ) {
			$errors = $validator->errors()->getMessages();

			$response->return = false;
			$response->errors = $errors;

			$response->msg = 'Some fields are not valid!';

			return response()->json( $response );
		}

		$init_password           = str_random( 12 );
		$activated_code          = str_random( 30 );
		$input['password']       = bcrypt( $init_password );
		$input['activated_code'] = $activated_code;

		$user                 = User::create( $input );
		$response->employee   = $user;
		$response->http_refer = route( 'home' );

		Mail::send( 'auth.emails.create', [
			'password' => $init_password,
			'user'     => $user
		], function ( $m ) use ( $user ) {
			$m->to( $user->email, $user->name )->subject( 'Your account created.' );
		} );

		return response()->json( $response );
	}
}
