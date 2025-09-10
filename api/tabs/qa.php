<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit('Please log in.');
}
?>
<div class="panel">
  <h2>Q&A</h2>
  <p>Have a question? Check here for answers or submit your own below.</p>
  <form method="post" style="margin-top:1rem;">
    <textarea name="question" rows="4" style="width:100%; padding:0.6rem;"></textarea>
    <button type="submit" class="btn" style="margin-top:0.5rem;">Submit</button>
  </form>
</div>
