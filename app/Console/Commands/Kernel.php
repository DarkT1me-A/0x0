protected function schedule(Schedule $schedule)
{
    $schedule->command('files:cleanup')->everyFiveMinutes();
}
