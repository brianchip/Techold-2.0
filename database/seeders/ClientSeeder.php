<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Project;
use App\Models\Employee;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample clients
        $clients = [
            [
                'client_code' => 'CLI001',
                'company_name' => 'AcmeCorp Engineering',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@acmecorp.com',
                'phone' => '+1-555-0123',
                'address' => '123 Industrial Ave, Tech City, CA 90210',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_code' => 'CLI002',
                'company_name' => 'GloboTech Solutions',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah.johnson@globotech.com',
                'phone' => '+1-555-0456',
                'address' => '456 Business Park Dr, Innovation City, NY 10001',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_code' => 'CLI003',
                'company_name' => 'Meridian Construction',
                'contact_person' => 'Michael Davis',
                'email' => 'michael.davis@meridian.com',
                'phone' => '+1-555-0789',
                'address' => '789 Builder\'s Lane, Construction City, TX 75001',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_code' => 'CLI004',
                'company_name' => 'FutureBuild Industries',
                'contact_person' => 'Emily Rodriguez',
                'email' => 'emily.rodriguez@futurebuild.com',
                'phone' => '+1-555-0321',
                'address' => '321 Future Blvd, Tomorrow City, FL 33101',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'client_code' => 'CLI005',
                'company_name' => 'TechnoStruct Ltd.',
                'contact_person' => 'David Wilson',
                'email' => 'david.wilson@technostruct.com',
                'phone' => '+1-555-0654',
                'address' => '654 Technology Way, Digital City, WA 98101',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }

        // Create some sample employees for project managers
        $employees = [
            [
                'employee_code' => 'EMP001',
                'first_name' => 'Alex',
                'last_name' => 'Thompson',
                'email' => 'alex.thompson@techold.com',
                'phone' => '+1-555-1001',
                'position' => 'Senior Project Manager',
                'department' => 'Engineering',
                'hourly_rate' => 75.00,
                'skills' => json_encode(['Project Management', 'Engineering', 'Leadership']),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_code' => 'EMP002',
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@techold.com',
                'phone' => '+1-555-1002',
                'position' => 'Project Manager',
                'department' => 'Engineering',
                'hourly_rate' => 65.00,
                'skills' => json_encode(['Project Management', 'Engineering', 'Communication']),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_code' => 'EMP003',
                'first_name' => 'Robert',
                'last_name' => 'Chen',
                'email' => 'robert.chen@techold.com',
                'phone' => '+1-555-1003',
                'position' => 'Senior Project Manager',
                'department' => 'Construction',
                'hourly_rate' => 80.00,
                'skills' => json_encode(['Project Management', 'Construction', 'Safety Management']),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        // Create some sample projects
        $clients = Client::all();
        $employees = Employee::all();

        $projects = [
            [
                'project_name' => 'Industrial Plant Automation System',
                'project_type' => 'EPC',
                'client_id' => $clients[0]->id,
                'project_manager_id' => $employees[0]->id,
                'description' => 'Complete automation system installation for manufacturing plant including SCADA, PLC programming, and HMI development.',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'status' => 'In Progress',
                'total_budget' => 750000.00,
                'actual_cost' => 245000.00,
                'progress_percent' => 35,
                'location' => 'Dallas, TX',
                'created_at' => Carbon::now()->subMonths(3),
                'updated_at' => Carbon::now(),
            ],
            [
                'project_name' => 'Office Building Electrical Infrastructure',
                'project_type' => 'Installation',
                'client_id' => $clients[1]->id,
                'project_manager_id' => $employees[1]->id,
                'description' => 'Complete electrical infrastructure for 20-story office building including power distribution, lighting systems, and emergency backup.',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(6),
                'status' => 'In Progress',
                'total_budget' => 450000.00,
                'actual_cost' => 95000.00,
                'progress_percent' => 20,
                'location' => 'New York, NY',
                'created_at' => Carbon::now()->subMonths(1),
                'updated_at' => Carbon::now(),
            ],
            [
                'project_name' => 'Smart Grid Implementation',
                'project_type' => 'Engineering',
                'client_id' => $clients[2]->id,
                'project_manager_id' => $employees[2]->id,
                'description' => 'Design and implementation of smart grid technology for municipal power distribution network.',
                'start_date' => Carbon::now()->addMonths(1),
                'end_date' => Carbon::now()->addMonths(12),
                'status' => 'Planned',
                'total_budget' => 1200000.00,
                'actual_cost' => 0.00,
                'progress_percent' => 5,
                'location' => 'Austin, TX',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'project_name' => 'Data Center Power Systems',
                'project_type' => 'EPC',
                'client_id' => $clients[3]->id,
                'project_manager_id' => $employees[0]->id,
                'description' => 'Complete power infrastructure for new data center including UPS systems, backup generators, and cooling systems.',
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->subMonths(1),
                'status' => 'Completed',
                'total_budget' => 800000.00,
                'actual_cost' => 785000.00,
                'progress_percent' => 100,
                'location' => 'Miami, FL',
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(1),
            ],
            [
                'project_name' => 'Manufacturing Equipment Installation',
                'project_type' => 'Installation',
                'client_id' => $clients[4]->id,
                'project_manager_id' => $employees[1]->id,
                'description' => 'Installation and commissioning of new manufacturing equipment line with automated quality control systems.',
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addMonths(4),
                'status' => 'In Progress',
                'total_budget' => 350000.00,
                'actual_cost' => 45000.00,
                'progress_percent' => 15,
                'location' => 'Seattle, WA',
                'created_at' => Carbon::now()->subWeeks(2),
                'updated_at' => Carbon::now(),
            ],
            [
                'project_name' => 'Renewable Energy Integration',
                'project_type' => 'Engineering',
                'client_id' => $clients[0]->id,
                'project_manager_id' => $employees[2]->id,
                'description' => 'Integration of solar and wind power systems into existing electrical infrastructure with battery storage.',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(3),
                'status' => 'On Hold',
                'total_budget' => 500000.00,
                'actual_cost' => 125000.00,
                'progress_percent' => 25,
                'location' => 'Phoenix, AZ',
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('Sample clients, employees, and projects created successfully!');
    }
}