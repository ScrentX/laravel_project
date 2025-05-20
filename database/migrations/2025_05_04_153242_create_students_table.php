<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->enum('course', ['BSBA', 'BSIT', 'BSED', 'Computer Science', 'Engineering', 'Business', 'Psychology', 'Mathematics', 'Biology', 'Chemistry', 'Physics', 'Economics', 'History']);
            $table->string('year_level');
            $table->string('contact_number');
            $table->string('password');
            $table->timestamps();
        });

        // Insert sample data
        DB::table('students')->insert([
            [
                'student_id' => 'ST20230001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@university.edu',
                'course' => 'Computer Science',
                'year_level' => '3rd',
                'contact_number' => '09123456789',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@university.edu',
                'course' => 'Engineering',
                'year_level' => '2nd',
                'contact_number' => '09234567890',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230003',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.j@university.edu',
                'course' => 'Business',
                'year_level' => '1st',
                'contact_number' => '09345678901',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230004',
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.w@university.edu',
                'course' => 'Psychology',
                'year_level' => '4th',
                'contact_number' => '09456789012',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230005',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.b@university.edu',
                'course' => 'Mathematics',
                'year_level' => '2nd',
                'contact_number' => '09567890123',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230006',
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.d@university.edu',
                'course' => 'Biology',
                'year_level' => '3rd',
                'contact_number' => '09678901234',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230007',
                'first_name' => 'Robert',
                'last_name' => 'Miller',
                'email' => 'robert.m@university.edu',
                'course' => 'Chemistry',
                'year_level' => '1st',
                'contact_number' => '09789012345',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230008',
                'first_name' => 'Jennifer',
                'last_name' => 'Wilson',
                'email' => 'jennifer.w@university.edu',
                'course' => 'Physics',
                'year_level' => '4th',
                'contact_number' => '09890123456',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230009',
                'first_name' => 'Thomas',
                'last_name' => 'Moore',
                'email' => 'thomas.m@university.edu',
                'course' => 'Economics',
                'year_level' => '2nd',
                'contact_number' => '09901234567',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 'ST20230010',
                'first_name' => 'Jessica',
                'last_name' => 'Taylor',
                'email' => 'jessica.t@university.edu',
                'course' => 'History',
                'year_level' => '3rd',
                'contact_number' => '09112345678',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};