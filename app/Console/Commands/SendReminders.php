<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for tasks due today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $this->info("Looking for reminders due on: " . $today->toDateString());
        
        // Find all reminders due today that have not been sent yet and have an email address
        $reminders = Reminder::where('reminder_date', '>=', $today->startOfDay())
            ->where('reminder_date', '<=', $today->copy()->endOfDay())
            ->where('email_sent', false)
            ->whereNotNull('reminder_email')
            ->where('is_completed', false)
            ->get();
            
        $count = $reminders->count();
        $this->info("Found {$count} reminders to send.");
        
        if ($count === 0) {
            return 0;
        }
        
        $sent = 0;
        foreach ($reminders as $reminder) {
            try {
                // Send the email
                Mail::to($reminder->reminder_email)
                    ->send(new ReminderMail($reminder));
                
                // Update the reminder to mark it as sent
                $reminder->email_sent = true;
                $reminder->save();
                
                $this->info("Sent reminder '{$reminder->title}' to {$reminder->reminder_email}");
                $sent++;
                
            } catch (\Exception $e) {
                $this->error("Failed to send reminder ID {$reminder->id}: " . $e->getMessage());
                Log::error("Failed to send reminder", [
                    'reminder_id' => $reminder->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Successfully sent {$sent} out of {$count} reminders.");
        return 0;
    }
}
