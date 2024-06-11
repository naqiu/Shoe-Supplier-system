<?php
include 'header.php';
?>

<style>
  /* Additional styling for demonstration purposes */
  .col {
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    text-align: center;
    padding: 20px 0;
    margin-bottom: 10px;
  }
</style>

<h2>VANtastic Shoes Supplier</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore
  magna aliqua.
  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
  Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
</p>
<!-- Button -->
<button class="btn">Button</button>

<!-- Text input -->
<input type="text" class="input" placeholder="Text Input">

<!-- Dropdown -->
<div class="dropdown">
  <button class="btn">Dropdown</button>
  <div class="dropdown-content">
    <a href="#">Option 1</a>
    <a href="#">Option 2</a>
    <a href="#">Option 3</a>
  </div>
</div>

<!-- Example select input -->
<select class="select">
  <option value="option1">Option 1</option>
  <option value="option2">Option 2</option>
  <option value="option3">Option 3</option>
</select>

<div class="container-fluid mt-2">
  <div class="row">
    <div class="col col-12">
      Full width on all screen sizes
    </div>
  </div>
  <div class="row">
    <div class="col col-6">
      Half width on all screen sizes
    </div>
    <div class="col col-6">
      Half width on all screen sizes
    </div>
  </div>
  <div class="row">
    <div class="col col-4">
      One-third width on all screen sizes
    </div>
    <div class="col col-4">
      One-third width on all screen sizes
    </div>
    <div class="col col-4">
      One-third width on all screen sizes
    </div>
  </div>
  <div class="row">
    <div class="col col-12 col-md-6">
      Full width on extra small screens, half width on medium screens and above
    </div>
    <div class="col col-12 col-md-6">
      Full width on extra small screens, half width on medium screens and above
    </div>
  </div>
</div>

<?php
include 'footer.php';
?>