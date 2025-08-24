<?php
// init_db.php
require_once 'config/database.php';

// Run the initialization function
initializeDatabase($conn);

echo "Database initialized successfully!";
?>