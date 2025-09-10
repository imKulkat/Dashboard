<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit('Please log in.');
}
?>
<div class="panel">
  <h2>Website Request Form</h2>
  <p>Request a new website or feature to be added to the dashboard.</p>
  <form method="post" style="margin-top:1rem;">
    <input type="text" name="site_name" placeholder="Website Name" style="width:100%; padding:0.6rem; margin-bottom:0.5rem;">
    <input type="url" name="site_url" placeholder="Website URL" style="width:100%; padding:0.6rem; margin-bottom:0.5rem;">
    <textarea name="details" rows="4" placeholder="Additional details..." style="width:100%; padding:0.6rem;"></textarea>
    <button type="submit" class="btn" style="margin-top:0.5rem;">Submit</button>
  </form>
</div>
