<?php
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header('Location: ../auth/login.php');
    exit();
}

// Get messages
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$oldInput = isset($_SESSION['old_input']) ? $_SESSION['old_input'] : [];
unset($_SESSION['errors'], $_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Flight</title>
    <link rel="stylesheet" href="../../css/company/company-styles.css">
    <?php include '../../navbar/navbar_css.php'; ?>

</head>
<body>


 <?php include '../../navbar/navbar.php'; ?>

<!-- Main Content -->
<div class="container" style="max-width: 800px; margin-top: 3rem;">
    
    <!-- Alert Messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Add Flight Form -->
    <div class="content-card">
        <div class="card-header">
            <div>
                <h1 class="card-title">Add New Flight</h1>
                <p style="color: oklch(0.4 0 0); margin-top: 0.5rem;">Create a new flight listing</p>
            </div>
        </div>
        
        <form action="../../../Backend/services/flight.php?action=add" method="POST" id="addFlightForm">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Flight Name</label>
                    <input 
                        type="text" 
                        name="flight_name" 
                        class="form-control" 
                        placeholder="e.g. New York Express"
                        value="<?php echo isset($oldInput['flight_name']) ? htmlspecialchars($oldInput['flight_name']) : ''; ?>"
                        required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Flight Code</label>
                    <input 
                        type="text" 
                        name="flight_code" 
                        class="form-control" 
                        placeholder="e.g. SK101"
                        value="<?php echo isset($oldInput['flight_code']) ? htmlspecialchars($oldInput['flight_code']) : ''; ?>"
                        required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Itinerary (Cities)</label>
                <textarea 
                    name="itinerary" 
                    class="form-control" 
                    placeholder="e.g. New York → Chicago → Los Angeles"
                    rows="3"
                    required><?php echo isset($oldInput['itinerary']) ? htmlspecialchars($oldInput['itinerary']) : ''; ?></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Ticket Price ($)</label>
                    <input 
                        type="number" 
                        name="fees" 
                        class="form-control" 
                        placeholder="350.00"
                        step="0.01"
                        min="0"
                        value="<?php echo isset($oldInput['fees']) ? htmlspecialchars($oldInput['fees']) : ''; ?>"
                        required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Max Passengers</label>
                    <input 
                        type="number" 
                        name="max_passengers" 
                        class="form-control" 
                        placeholder="150"
                        min="1"
                        value="<?php echo isset($oldInput['max_passengers']) ? htmlspecialchars($oldInput['max_passengers']) : ''; ?>"
                        required>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Departure Date & Time</label>
                    <input 
                        type="datetime-local" 
                        name="start_datetime" 
                        class="form-control"
                        value="<?php echo isset($oldInput['start_datetime']) ? htmlspecialchars($oldInput['start_datetime']) : ''; ?>"
                        required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Arrival Date & Time</label>
                    <input 
                        type="datetime-local" 
                        name="end_datetime" 
                        class="form-control"
                        value="<?php echo isset($oldInput['end_datetime']) ? htmlspecialchars($oldInput['end_datetime']) : ''; ?>"
                        required>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Add Flight</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
</div>

<script>
document.getElementById('addFlightForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.querySelector('[name="start_datetime"]').value);
    const endDate = new Date(document.querySelector('[name="end_datetime"]').value);
    
    if (endDate <= startDate) {
        e.preventDefault();
        alert('Arrival date must be after departure date');
    }
});
</script>

</body>
</html>