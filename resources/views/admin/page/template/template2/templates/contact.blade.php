 <body>
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link type="text/css" href="{{asset('assets/frontend/template2/css/front.css')}}" rel="stylesheet" />

     <!-- Navbar -->
     [[Header-Template]]

     <!-- Contact Hero -->
     <section class="contact-hero">
         <div class="contact-hero-content">
             <h1>Get In Touch</h1>
             <p>Have a question or need help? We're here to assist you. Reach out to us and we'll respond as soon as possible.</p>
         </div>
     </section>

     <!-- Contact Section -->
     <section class="contact-section">
         <div class="contact-container">
             <!-- Contact Info -->
             <div class="contact-info">
                 <div>
                     <h2>Contact Information</h2>
                     <p>Feel free to reach out to us through any of these channels. Our team is available to help you with any questions or concerns.</p>
                 </div>

                 <div class="info-item">
                     <div class="info-icon">
                         <i class="fas fa-map-marker-alt"></i>
                     </div>
                     <div class="info-content">
                         <h3>Our Office</h3>
                         <p>Jl. Contoh No. 123<br>Jakarta, Indonesia 12345</p>
                     </div>
                 </div>

                 <div class="info-item">
                     <div class="info-icon">
                         <i class="fas fa-phone"></i>
                     </div>
                     <div class="info-content">
                         <h3>Phone Number</h3>
                         <p><a href="tel:+6281234567890">+62 812-3456-7890</a></p>
                         <p><a href="tel:+6287654321098">+62 876-5432-1098</a></p>
                     </div>
                 </div>

                 <div class="info-item">
                     <div class="info-icon">
                         <i class="fas fa-envelope"></i>
                     </div>
                     <div class="info-content">
                         <h3>Email Address</h3>
                         <p><a href="mailto:support@replai.id">support@replai.id</a></p>
                         <p><a href="mailto:sales@replai.id">sales@replai.id</a></p>
                     </div>
                 </div>

                 <div class="info-item">
                     <div class="info-icon">
                         <i class="fas fa-clock"></i>
                     </div>
                     <div class="info-content">
                         <h3>Working Hours</h3>
                         <p>Monday - Friday: 09:00 - 18:00</p>
                         <p>Saturday: 09:00 - 14:00</p>
                         <p>Sunday: Closed</p>
                     </div>
                 </div>

                 <div class="social-contact">
                     <h3>Follow Us</h3>
                     <div class="social-icons">
                         <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                         <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                         <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                         <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                         <a href="#" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                         <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                     </div>
                 </div>
             </div>
         </div>
     </section>

     <!-- Map Section -->
     <section class="map-section">
         <div class="map-container">
             <h2>Find Us On Map</h2>
             <div class="map-wrapper">
                 <!-- Replace with your actual Google Maps embed code -->
                 <iframe
                     src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613!3d-6.1944491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sJakarta!5e0!3m2!1sen!2sid!4v1234567890"
                     allowfullscreen=""
                     loading="lazy"
                     referrerpolicy="no-referrer-when-downgrade">
                 </iframe>
             </div>
         </div>
     </section>

     <!-- FAQ Section -->
     <section class="faq-section">
         <div class="section-header">
             <h2>Frequently Asked Questions</h2>
             <p>Quick answers to common questions about contacting us</p>
         </div>
         <div class="faq-container">
             <div class="faq-item">
                 <button class="faq-question">
                     <h3>How quickly will I receive a response?</h3>
                     <div class="faq-icon"><i class="fas fa-chevron-down"></i></div>
                 </button>
                 <div class="faq-answer">
                     <div class="faq-answer-content">
                         We aim to respond to all inquiries within 24 hours during business days. For urgent matters, please call our phone number directly.
                     </div>
                 </div>
             </div>

             <div class="faq-item">
                 <button class="faq-question">
                     <h3>Can I schedule a demo or consultation?</h3>
                     <div class="faq-icon"><i class="fas fa-chevron-down"></i></div>
                 </button>
                 <div class="faq-answer">
                     <div class="faq-answer-content">
                         Yes! Simply mention your preferred date and time in the message form, and our team will coordinate with you to schedule a personalized demo or consultation.
                     </div>
                 </div>
             </div>

             <div class="faq-item">
                 <button class="faq-question">
                     <h3>Do you offer technical support?</h3>
                     <div class="faq-icon"><i class="fas fa-chevron-down"></i></div>
                 </button>
                 <div class="faq-answer">
                     <div class="faq-answer-content">
                         Absolutely! Our technical support team is available to help you with any issues or questions related to our platform. Email us at support@replai.id for technical assistance.
                     </div>
                 </div>
             </div>

             <div class="faq-item">
                 <button class="faq-question">
                     <h3>What's the best way to reach you for sales inquiries?</h3>
                     <div class="faq-icon"><i class="fas fa-chevron-down"></i></div>
                 </button>
                 <div class="faq-answer">
                     <div class="faq-answer-content">
                         For sales inquiries, you can email sales@replai.id or use the contact form above. Our sales team will get back to you with detailed information about our packages and pricing.
                     </div>
                 </div>
             </div>
         </div>
     </section>

     [[Footer-Template]]
     [[Scripts-Template]]
 </body>