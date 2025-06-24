#!/bin/bash

# Task Scheduler CRON Setup Script
# This script sets up a CRON job to run the task reminder system every hour

# Get the current directory (where the script is located)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_PHP_FILE="$SCRIPT_DIR/cron.php"

# Check if cron.php exists
if [ ! -f "$CRON_PHP_FILE" ]; then
    echo "Error: cron.php not found in $SCRIPT_DIR"
    exit 1
fi

# Get the current user's crontab
TEMP_CRON_FILE="/tmp/cron_temp_$$"
crontab -l > "$TEMP_CRON_FILE" 2>/dev/null || echo "" > "$TEMP_CRON_FILE"

# Check if the cron job already exists
CRON_COMMAND="0 * * * * /usr/bin/php $CRON_PHP_FILE"

if grep -q "$CRON_PHP_FILE" "$TEMP_CRON_FILE"; then
    echo "CRON job for Task Scheduler already exists. Updating..."
    # Remove existing entry
    grep -v "$CRON_PHP_FILE" "$TEMP_CRON_FILE" > "${TEMP_CRON_FILE}.new"
    mv "${TEMP_CRON_FILE}.new" "$TEMP_CRON_FILE"
fi

# Add the new cron job (runs every hour at minute 0)
echo "$CRON_COMMAND" >> "$TEMP_CRON_FILE"

# Install the updated crontab
if crontab "$TEMP_CRON_FILE"; then
    echo "CRON job successfully installed!"
    echo "Task reminder emails will be sent every hour."
    echo "Cron job: $CRON_COMMAND"
else
    echo "Failed to install CRON job. Please check your system permissions."
    rm -f "$TEMP_CRON_FILE"
    exit 1
fi

# Clean up temporary file
rm -f "$TEMP_CRON_FILE"

# Create initial data files if they don't exist
touch "$SCRIPT_DIR/tasks.txt"
touch "$SCRIPT_DIR/subscribers.txt"
touch "$SCRIPT_DIR/pending_subscriptions.txt"

# Set proper permissions
chmod 644 "$SCRIPT_DIR/tasks.txt"
chmod 644 "$SCRIPT_DIR/subscribers.txt"
chmod 644 "$SCRIPT_DIR/pending_subscriptions.txt"
chmod 644 "$SCRIPT_DIR/cron_log.txt" 2>/dev/null || touch "$SCRIPT_DIR/cron_log.txt"

echo ""
echo "Setup completed successfully!"
echo "Data files created/verified:"
echo "- tasks.txt"
echo "- subscribers.txt"
echo "- pending_subscriptions.txt"
echo "- cron_log.txt (for logging)"
echo ""
echo "To view current cron jobs: crontab -l"
echo "To remove the cron job later: crontab -e (then delete the line)"
echo ""
echo "The system is now ready to send hourly task reminders!"