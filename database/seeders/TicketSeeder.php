<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Ticket_category;
use App\Models\User;
use App\Models\Store\Store;
use App\Models\Master\Label;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing tickets
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ticket::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get required references
        $categories = ticket_category::all();
        $users = User::limit(5)->get();
        $labels = Label::limit(3)->get();

        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please ensure you have categories and users seeded first');
            return;
        }

        // Sample ticket data
        $ticketData = [
            [
                'ticket_name' => 'Website Loading Issue',
                'title' => 'Website takes too long to load',
                'notes' => 'The website is taking more than 30 seconds to load. Users are experiencing timeout issues.',
                'priority' => 'high',
                'status' => 'open',
                'contact_name' => 'John Doe',
                'contact_email' => 'john.doe@example.com',
                'contact_phone' => '+6281234567890'
            ],
            [
                'ticket_name' => 'Payment Gateway Error',
                'title' => 'Payment processing fails at checkout',
                'notes' => 'Customers cannot complete their purchases. Payment gateway returns error 500.',
                'priority' => 'high',
                'status' => 'in_progress',
                'contact_name' => 'Jane Smith',
                'contact_email' => 'jane.smith@example.com',
                'contact_phone' => '+6281234567891'
            ],
            [
                'ticket_name' => 'Mobile App Crash',
                'title' => 'App crashes on Android devices',
                'notes' => 'The mobile application crashes when users try to upload images on Android devices.',
                'priority' => 'medium',
                'status' => 'pending',
                'contact_name' => 'Michael Johnson',
                'contact_email' => 'michael.johnson@example.com',
                'contact_phone' => '+6281234567892'
            ],
            [
                'ticket_name' => 'Email Notification Bug',
                'title' => 'Users not receiving email notifications',
                'notes' => 'Email notifications for order confirmations and password resets are not being sent.',
                'priority' => 'medium',
                'status' => 'open',
                'contact_name' => 'Sarah Wilson',
                'contact_email' => 'sarah.wilson@example.com',
                'contact_phone' => '+6281234567893'
            ],
            [
                'ticket_name' => 'Database Performance',
                'title' => 'Slow database query performance',
                'notes' => 'Database queries are running slower than expected, affecting overall application performance.',
                'priority' => 'medium',
                'status' => 'resolved',
                'contact_name' => 'David Brown',
                'contact_email' => 'david.brown@example.com',
                'contact_phone' => '+6281234567894'
            ],
            [
                'ticket_name' => 'User Interface Bug',
                'title' => 'Button alignment issue on checkout page',
                'notes' => 'The checkout button is not properly aligned on mobile devices, making it difficult to tap.',
                'priority' => 'low',
                'status' => 'open',
                'contact_name' => 'Lisa Davis',
                'contact_email' => 'lisa.davis@example.com',
                'contact_phone' => '+6281234567895'
            ],
            [
                'ticket_name' => 'Security Vulnerability',
                'title' => 'Potential SQL injection vulnerability',
                'notes' => 'Security audit revealed potential SQL injection vulnerability in the search functionality.',
                'priority' => 'high',
                'status' => 'in_progress',
                'contact_name' => 'Robert Miller',
                'contact_email' => 'robert.miller@example.com',
                'contact_phone' => '+6281234567896'
            ],
            [
                'ticket_name' => 'Feature Request',
                'title' => 'Add dark mode theme',
                'notes' => 'Users have requested a dark mode theme option for better user experience.',
                'priority' => 'low',
                'status' => 'pending',
                'contact_name' => 'Emily Garcia',
                'contact_email' => 'emily.garcia@example.com',
                'contact_phone' => '+6281234567897'
            ],
            [
                'ticket_name' => 'Data Export Issue',
                'title' => 'CSV export functionality not working',
                'notes' => 'Users cannot export their data to CSV format. The export button shows loading but never completes.',
                'priority' => 'medium',
                'status' => 'open',
                'contact_name' => 'Christopher Martinez',
                'contact_email' => 'christopher.martinez@example.com',
                'contact_phone' => '+6281234567898'
            ],
            [
                'ticket_name' => 'Integration Problem',
                'title' => 'Third-party API integration failing',
                'notes' => 'Integration with shipping provider API is failing intermittently, causing order processing delays.',
                'priority' => 'high',
                'status' => 'resolved',
                'contact_name' => 'Amanda Rodriguez',
                'contact_email' => 'amanda.rodriguez@example.com',
                'contact_phone' => '+6281234567899'
            ]
        ];

        foreach ($ticketData as $index => $data) {
            try {
                $ticket = new ticket();
                
                // Generate ticket ID (this will be auto-generated by the model)
                // Set basic ticket information
                $ticket->ticket_name = $data['ticket_name'];
                $ticket->title = $data['title'];
                $ticket->notes = $data['notes'];
                $ticket->priority = $data['priority'];
                $ticket->status = $data['status'];
                
                // Set contact information
                $ticket->contact_name = $data['contact_name'];
                $ticket->contact_email = $data['contact_email'];
                $ticket->contact_phone = $data['contact_phone'];
                
                // Assign random relationships
                $ticket->category_id = $categories->random()->id;
                $ticket->contact_id = $users->random()->id; // Use user as contact for now
                $ticket->label_id = $labels->random()->id;
                
                // Assign agent_id for some tickets (not all)
                if (in_array($data['status'], ['in_progress', 'resolved', 'closed'])) {
                    $ticket->agent_id = $users->random()->id;
                }
                
                // Set random dates
                $createdAt = now()->subDays(rand(1, 30));
                $ticket->created_at = $createdAt;
                $ticket->updated_at = $createdAt->addHours(rand(1, 48));
                
                $ticket->save();
                
                $this->command->info("Created ticket: {$ticket->ticket_id} - {$ticket->ticket_name}");
                
            } catch (\Exception $e) {
                $ticketNumber = $index + 1;
                $this->command->error("Failed to create ticket {$ticketNumber}: " . $e->getMessage());
            }
        }

        $this->command->info('Ticket seeding completed successfully!');
    }
}