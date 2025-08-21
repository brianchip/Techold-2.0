<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\BOQSection;
use App\Models\BOQItem;
use App\Models\ProjectMilestone;
use Carbon\Carbon;

class ComprehensiveProjectDataSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();
        if (!$project) {
            $this->command->error('No projects found. Please create a project first.');
            return;
        }

        $this->command->info("Adding comprehensive data to project: {$project->project_name}");

        // Create BOQ Section
        $section = BOQSection::create([
            'project_id' => $project->id,
            'section_name' => 'Electrical Installation',
            'section_code' => 'ELEC001',
            'description' => 'Electrical components and installation',
            'display_order' => 1,
            'status' => 'Active'
        ]);

        // Create BOQ Items
        BOQItem::create([
            'boq_section_id' => $section->id,
            'project_id' => $project->id,
            'item_code' => 'ELC001',
            'description' => 'Solar Panel 400W',
            'unit' => 'Each',
            'quantity' => 50.0,
            'rate' => 250.00,
            'category' => 'Materials',
            'status' => 'Approved'
        ]);

        BOQItem::create([
            'boq_section_id' => $section->id,
            'project_id' => $project->id,
            'item_code' => 'ELC002',
            'description' => 'Inverter 10kW',
            'unit' => 'Each',
            'quantity' => 2.0,
            'rate' => 2500.00,
            'category' => 'Equipment',
            'status' => 'Approved'
        ]);

        // Create Milestones
        ProjectMilestone::create([
            'project_id' => $project->id,
            'milestone_name' => 'Project Kickoff',
            'description' => 'Initial project setup and team alignment',
            'due_date' => $project->start_date,
            'completion_date' => $project->start_date,
            'status' => 'Completed',
            'progress_percent' => 100,
            'is_critical' => true
        ]);

        ProjectMilestone::create([
            'project_id' => $project->id,
            'milestone_name' => 'Design Phase Complete',
            'description' => 'All design documents approved and finalized',
            'due_date' => Carbon::now()->addDays(30),
            'status' => 'In Progress',
            'progress_percent' => 75,
            'is_critical' => true
        ]);

        $this->command->info('Comprehensive project data seeded successfully!');
    }
}