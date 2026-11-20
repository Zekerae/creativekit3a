<?php
/**
 * includes/quote-modal.php
 * ─────────────────────────────────────────────────────────────
 * Request a Quote multi-step modal component.
 * Included inside includes/footer.php.
 * ─────────────────────────────────────────────────────────────
 */
?>
<!-- ============================================================
     REQUEST A QUOTE MODAL  (#quote-modal)
     ============================================================
     HOW TO USE:
       1. Paste this block anywhere before </body> (alongside your
          existing login/register modals).
       2. Add quote-modal.css to your <head>.
       3. Add quote-modal.js before </body> (after main.js).
       4. The "Request a Quote" button in navbar already has class
          .btn-chat — just add onclick="openModal('quote-modal')"
          to it (see the btn-chat snippet below).

     NAVBAR BUTTON CHANGE (in index.html / category-page.html):
       BEFORE:
         <button class="btn-chat" aria-label="Request a Quote">Request a Quote</button>
       AFTER:
         <button class="btn-chat" onclick="openModal('quote-modal')" aria-label="Request a Quote">Request a Quote</button>

     Backend endpoint expected:
       POST /api/quote
       Body: { name, company, email, phone, product_type, quantity, budget, deadline, details }
       Response: { success: true } | { success: false, message: '...' }
     ============================================================ -->

<div class="lm-modal qm-modal" id="quote-modal" role="dialog" aria-modal="true" aria-labelledby="quote-modal-title">
  <div class="lm-card qm-card">

    <!-- Close button -->
    <button class="lm-close" onclick="closeModal('quote-modal')" aria-label="Close">&times;</button>

    <!-- ── Header ─────────────────────────────────────────── -->
    <div class="lm-header qm-header">
      <span class="lm-brand">CreativeKit3A</span>
      <p id="quote-modal-title" class="qm-title">Request a Quote</p>
      <p class="lm-tagline">Tell us what you need — we'll get back within 24 hours.</p>
    </div>

    <!-- ── Step indicator ─────────────────────────────────── -->
    <div class="qm-steps" aria-label="Form progress">
      <div class="qm-step active" id="qm-step-dot-1">
        <div class="qm-step-dot">1</div>
        <span>Your Info</span>
      </div>
      <div class="qm-step-line"></div>
      <div class="qm-step" id="qm-step-dot-2">
        <div class="qm-step-dot">2</div>
        <span>Product</span>
      </div>
      <div class="qm-step-line"></div>
      <div class="qm-step" id="qm-step-dot-3">
        <div class="qm-step-dot">3</div>
        <span>Details</span>
      </div>
    </div>

    <!-- ── STEP 1: Contact Info ────────────────────────────── -->
    <div class="lm-panel qm-panel" id="qm-panel-1">

      <div class="lm-field-row">
        <div class="lm-field">
          <label for="qm-name"><i class="fas fa-user"></i> Full Name <span class="qm-req">*</span></label>
          <input type="text" id="qm-name" placeholder="Juan Dela Cruz" autocomplete="name" required>
        </div>
        <div class="lm-field">
          <label for="qm-company"><i class="fas fa-building"></i> Company <span class="lm-optional">(optional)</span></label>
          <input type="text" id="qm-company" placeholder="ABC Corporation" autocomplete="organization">
        </div>
      </div>

      <div class="lm-field">
        <label for="qm-email"><i class="fas fa-envelope"></i> Email Address <span class="qm-req">*</span></label>
        <input type="email" id="qm-email" placeholder="you@company.com" autocomplete="email" required>
      </div>

      <div class="lm-field">
        <label for="qm-phone"><i class="fas fa-mobile-alt"></i> Mobile Number <span class="qm-req">*</span></label>
        <input type="tel" id="qm-phone" placeholder="+63 917 000 0000" autocomplete="tel" required>
      </div>

      <div class="lm-msg lm-msg--error" id="qm-err-1" hidden></div>

      <button class="btn btn-primary lm-submit" onclick="qmNextStep(1)">
        Next: Product Info <i class="fas fa-arrow-right"></i>
      </button>

    </div>

    <!-- ── STEP 2: Product Info ────────────────────────────── -->
    <div class="lm-panel qm-panel" id="qm-panel-2" hidden style="display:none;">

      <div class="lm-field">
        <label for="qm-product-type"><i class="fas fa-tag"></i> Product Category <span class="qm-req">*</span></label>
        <select id="qm-product-type" class="qm-select" required>
          <option value="" disabled selected>— Select a category —</option>
          <optgroup label="Accessories &amp; Gadgets">
            <option value="car-accessories">Car Accessories</option>
            <option value="computer-accessories">Computer Accessories</option>
            <option value="desk-accessories">Desk Accessories</option>
            <option value="mobile-accessories">Mobile Accessories</option>
            <option value="gadgets">Gadgets</option>
          </optgroup>
          <optgroup label="Apparel">
            <option value="caps">Caps</option>
            <option value="jackets">Jackets</option>
            <option value="shirts">Shirts</option>
          </optgroup>
          <optgroup label="Bags &amp; Pouches">
            <option value="backpacks">Backpacks</option>
            <option value="eco-bags">Eco Bags</option>
            <option value="laptop-bags">Laptop Bags</option>
            <option value="tote-bags">Tote Bags</option>
            <option value="travel-bags">Travel Bags</option>
          </optgroup>
          <optgroup label="Drinkware">
            <option value="tumblers">Tumblers &amp; Flasks</option>
            <option value="mugs">Mugs &amp; Cups</option>
            <option value="water-bottles">Water Bottles</option>
          </optgroup>
          <optgroup label="Other">
            <option value="gift-set">Gift Sets</option>
            <option value="home-living">Home Living</option>
            <option value="pen-paper">Pen &amp; Paper</option>
            <option value="pu-leather">PU Leather</option>
            <option value="other">Other / Not Listed</option>
          </optgroup>
        </select>
      </div>

      <div class="lm-field-row">
        <div class="lm-field">
          <label for="qm-quantity"><i class="fas fa-boxes"></i> Quantity <span class="qm-req">*</span></label>
          <select id="qm-quantity" class="qm-select" required>
            <option value="" disabled selected>— Select —</option>
            <option value="50-99">50 – 99 pcs</option>
            <option value="100-299">100 – 299 pcs</option>
            <option value="300-499">300 – 499 pcs</option>
            <option value="500-999">500 – 999 pcs</option>
            <option value="1000+">1,000+ pcs</option>
          </select>
        </div>
        <div class="lm-field">
          <label for="qm-budget"><i class="fas fa-peso-sign"></i> Budget Range <span class="lm-optional">(optional)</span></label>
          <select id="qm-budget" class="qm-select">
            <option value="" disabled selected>— Select —</option>
            <option value="under-5k">Under ₱5,000</option>
            <option value="5k-15k">₱5,000 – ₱15,000</option>
            <option value="15k-50k">₱15,000 – ₱50,000</option>
            <option value="50k-100k">₱50,000 – ₱100,000</option>
            <option value="100k+">₱100,000+</option>
          </select>
        </div>
      </div>

      <div class="lm-field">
        <label for="qm-deadline"><i class="fas fa-calendar-alt"></i> Needed By <span class="lm-optional">(optional)</span></label>
        <input type="date" id="qm-deadline" class="qm-date"
               min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
        <!-- If using plain HTML (no PHP), set min via JS in quote-modal.js -->
      </div>

      <div class="lm-msg lm-msg--error" id="qm-err-2" hidden></div>

      <div class="qm-btn-row">
        <button class="btn btn-outline qm-back-btn" onclick="qmPrevStep(2)">
          <i class="fas fa-arrow-left"></i> Back
        </button>
        <button class="btn btn-primary lm-submit qm-next-btn" onclick="qmNextStep(2)">
          Next: Details <i class="fas fa-arrow-right"></i>
        </button>
      </div>

    </div>

    <!-- ── STEP 3: Details & Submit ────────────────────────── -->
    <div class="lm-panel qm-panel" id="qm-panel-3" hidden style="display:none;">

      <div class="lm-field">
        <label for="qm-details">
          <i class="fas fa-pen-to-square"></i> Tell Us More
          <span class="lm-optional">(optional)</span>
        </label>
        <textarea id="qm-details" class="qm-textarea" rows="4"
                  placeholder="Describe your customization needs, logo placement, color preferences, event date, or anything else we should know..."></textarea>
      </div>

      <!-- Customization options checkboxes -->
      <div class="lm-field">
        <label><i class="fas fa-paint-brush"></i> Customization Needed</label>
        <div class="qm-checkboxes">
          <label class="qm-check-item"><input type="checkbox" name="qm-custom" value="logo-print"> Logo Printing</label>
          <label class="qm-check-item"><input type="checkbox" name="qm-custom" value="laser-engrave"> Laser Engraving</label>
          <label class="qm-check-item"><input type="checkbox" name="qm-custom" value="embroidery"> Embroidery</label>
          <label class="qm-check-item"><input type="checkbox" name="qm-custom" value="sublimation"> Sublimation</label>
          <label class="qm-check-item"><input type="checkbox" name="qm-custom" value="packaging"> Custom Packaging</label>
          <label class="qm-check-item"><input type="checkbox" name="qm-custom" value="not-sure"> Not Sure Yet</label>
        </div>
      </div>

      <!-- How did you hear about us -->
      <div class="lm-field">
        <label for="qm-source">
          <i class="fas fa-map-marker-alt"></i> How did you find us?
          <span class="lm-optional">(optional)</span>
        </label>
        <select id="qm-source" class="qm-select">
          <option value="" disabled selected>— Select —</option>
          <option value="facebook">Facebook</option>
          <option value="google">Google Search</option>
          <option value="referral">Referral / Word of Mouth</option>
          <option value="instagram">Instagram</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div class="lm-msg lm-msg--error"   id="qm-err-3"     hidden></div>
      <div class="lm-msg lm-msg--success" id="qm-success"   hidden></div>

      <div class="qm-btn-row">
        <button class="btn btn-outline qm-back-btn" id="qm-back-btn-3" onclick="qmPrevStep(3)">
          <i class="fas fa-arrow-left"></i> Back
        </button>
        <button class="btn btn-primary lm-submit qm-next-btn" id="qm-submit-btn" onclick="submitQuote()">
          <i class="fas fa-paper-plane"></i> Send Quote Request
        </button>
      </div>

    </div>

    <!-- ── SUCCESS STATE ───────────────────────────────────── -->
    <div class="qm-panel qm-success-panel" id="qm-panel-success" hidden style="display:none;">
      <div class="qm-success-icon">
        <i class="fas fa-circle-check"></i>
      </div>
      <h3 class="qm-success-title">Quote Request Sent!</h3>
      <p class="qm-success-msg">
        Thank you! We've received your request and will send a detailed quote to
        <strong id="qm-confirm-email"></strong> within <strong>24 hours</strong>.
      </p>
      <p class="qm-success-contact">
        Need it faster? Call us at
        <a href="tel:+639177142774">+63 917 714 2774</a> or
        <a href="tel:+639339927410">+63 933 992 7410</a>
      </p>
      <button class="btn btn-primary lm-submit" onclick="closeModal('quote-modal')">
        <i class="fas fa-check"></i> Done
      </button>
    </div>

  </div><!-- /.lm-card -->
</div><!-- /#quote-modal -->