<?php
include 'db_connect.php';
$service_types = $conn->query("SELECT id, name FROM service_types");
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raise a Support Ticket - Airtel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts - Airtel uses Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --airtel-red: #e20000;
            --airtel-dark-red: #c10000;
            --airtel-black: #222222;
            --airtel-gray: #f5f5f5;
            --airtel-light-gray: #f9f9f9;
            --airtel-blue: #0066cc;
        }
        
        body {
            background-color: var(--airtel-gray);
            font-family: 'Roboto', sans-serif;
            color: var(--airtel-black);
        }
        
        .ticket-form {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-top: 4px solid var(--airtel-red);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .airtel-logo {
            height: 40px;
            margin-bottom: 20px;
        }
        
        .form-title {
            color: var(--airtel-red);
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .form-subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--airtel-black);
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--airtel-red);
            box-shadow: 0 0 0 0.25rem rgba(226, 0, 0, 0.15);
        }
        
        .form-control::placeholder {
            color: #aaa;
            font-weight: 300;
        }
        
        #email-warning {
            color: var(--airtel-red);
            font-size: 13px;
            margin-top: 5px;
            font-weight: 400;
        }
        
        #submitBtn {
            background-color: var(--airtel-red);
            border: none;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        #submitBtn:hover {
            background-color: var(--airtel-dark-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(226, 0, 0, 0.2);
        }
        
        #submitBtn:active {
            transform: translateY(0);
        }
        
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        
        .loading-content {
            text-align: center;
            color: white;
        }
        
        .loading-video {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
        }
        
        .loading-text {
            font-size: 18px;
            font-weight: 500;
            margin-top: 15px;
        }
        
        .loading-subtext {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        /* Input animations */
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating label {
            color: #777;
            font-weight: 400;
        }
        
        /* Modal styling */
        .modal-header {
            background-color: var(--airtel-red);
            color: white;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        /* Touch effects */
        .btn, .form-control, .form-select {
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .ticket-form {
                margin: 20px auto;
                padding: 20px;
            }
            
            .form-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="ticket-form">
        <div class="form-header">
            <!-- Airtel Logo - Replace with your actual logo path -->
            <img src="images/logo.jpeg" alt="SunfaceTechs" class="airtel-logo">
            <h3 class="form-title">Raise a Support Ticket</h3>
            <p class="form-subtitle">We're here to help you with any issues you're facing</p>
        </div>
        
        <form id="ticketForm">
            <div class="mb-4">
                <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
                <div class="invalid-feedback">Please provide your name</div>
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">Your Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Enter your email address">
                <div id="email-warning"></div>
                <div class="invalid-feedback">Please provide a valid email</div>
            </div>

            <div class="mb-4">
                <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                <select name="service_type" class="form-select" required>
                    <option value="" disabled selected>Select a service</option>
                    <?php while ($row = $service_types->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <div class="invalid-feedback">Please select a service type</div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Issue Description <span class="text-danger">*</span></label>
                <textarea name="description" rows="5" class="form-control" required placeholder="Describe your issue in detail..."></textarea>
                <div class="invalid-feedback">Please describe your issue</div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-3">
                    <i class="fas fa-paper-plane me-2"></i> Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay">
    <div class="loading-content">
        <video autoplay loop muted class="loading-video">
            <source src="images/loading2.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="loading-text">Processing your request</div>
        <div class="loading-subtext">Please wait while we generate your ticket</div>
        <div class="spinner-border text-light mt-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>
  
<!-- Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ticketModalLabel"><i class="fas fa-ticket-alt me-2"></i> Ticket Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="ticketModalMessage"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
    // Add input focus effects
    $('.form-control, .form-select').on('focus', function() {
        $(this).parent().find('.form-label').css('color', 'var(--airtel-red)');
    }).on('blur', function() {
        $(this).parent().find('.form-label').css('color', 'var(--airtel-black)');
    });

    // Duplicate email check
    $('#email').on('blur', function () {
        const email = $(this).val().trim();
        if (email.length === 0) return;

        $.ajax({
            url: 'check_email.php',
            type: 'POST',
            data: { email: email },
            success: function (response) {
                if (response === 'exists') {
                    $('#email-warning').html('<i class="fas fa-exclamation-circle me-2"></i> We are currently working on your issue. Please wait until your existing ticket is resolved.');
                    $('#submitBtn').prop('disabled', true).css('opacity', '0.7');
                } else {
                    $('#email-warning').text('');
                    $('#submitBtn').prop('disabled', false).css('opacity', '1');
                }
            }
        });
    });

    // Form validation
    $('#ticketForm').on('submit', function (e) {
        e.preventDefault();
        
        // Validate form
        let isValid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) return;
        
        // Show loading overlay
        $('#loading-overlay').fadeIn();
        $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...');

        $.ajax({
            url: 'submit_ticket.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                // Hide loading overlay
                $('#loading-overlay').fadeOut();
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Submit Ticket');
                
                $('#ticketModalMessage').html(response.message);
                const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
                modal.show();

                if (response.success) {
                    $('#ticketForm')[0].reset();
                }
            },
            error: function () {
                // Hide loading overlay on error too
                $('#loading-overlay').fadeOut();
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Submit Ticket');
                
                $('#ticketModalMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> An unexpected error occurred. Please try again.</div>');
                const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
                modal.show();
            }
        });
    });
    
    // Remove invalid class when user starts typing
    $('input, textarea, select').on('input change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
</body>
</html>