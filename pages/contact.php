<?php
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$phone || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $to = 'info@opdefence.com';
        $headers = "From: $email\r\nReply-To: $email\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
        $body = "
        <html>
        <body style='font-family:Arial,sans-serif;padding:20px;'>
            <h2>New Enquiry from O.P Defence Enterprises Website</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject ?: 'N/A') . "</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        </body>
        </html>";
        if (mail($to, "Website Enquiry: " . ($subject ?: 'New Contact'), $body, $headers)) {
            $success = 'Thank you for your message! We will get back to you shortly.';
        } else {
            $error = 'Sorry, something went wrong. Please try again later.';
        }
    }
}
?>

<div class="page-banner">
    <h1>Contact Us</h1>
    <p>Get in touch with us for inquiries, quotations, or any assistance</p>
</div>

<div class="page-container">
    <div class="contact-wrapper">

        <!-- Contact Info -->
        <div class="contact-info-section">
            <h2>Get In Touch</h2>

            <div class="contact-detail-item">
                <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="detail">
                    <h3>Our Address</h3>
                    <p>Plot No. 134, Sector 4, UIT, Bhiwadi-301019, Rajasthan, India</p>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="icon"><i class="fa-solid fa-phone"></i></div>
                <div class="detail">
                    <h3>Phone Number</h3>
                    <p><a href="tel:09079106342">+91 9079106342</a></p>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="icon"><i class="fa-regular fa-envelope"></i></div>
                <div class="detail">
                    <h3>Email Address</h3>
                    <p><a href="mailto:info@opdefence.com">info@opdefence.com</a></p>
                </div>
            </div>

            <div class="contact-detail-item">
                <div class="icon"><i class="fa-regular fa-clock"></i></div>
                <div class="detail">
                    <h3>Working Hours</h3>
                    <p>Monday - Saturday: 9:00 AM - 6:00 PM<br>Sunday: Closed</p>
                </div>
            </div>

            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3515.7081539776525!2d76.811428!3d28.213019!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjjCsDEyJzQ2LjkiTiA3NsKwNDgnNDEuMSJF!5e0!3m2!1sen!2sin!4v1" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-section">
            <h2>Send Us a Message</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="?page=contact">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Message <span class="required">*</span></label>
                    <textarea name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" name="submit_contact" class="submit-btn">
                    <i class="fa-regular fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

    </div>
</div>