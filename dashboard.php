<?php 
include 'config.php'; 
include 'header.php'; 
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h1>Welcome, <?= $user['name'] ?></h1>
    <input type="text" id="search" class="form-control mb-3" placeholder="Search assignments...">
    <div id="results" class="list-group"></div>
</div>

<script>
const search = document.getElementById('search');
search.addEventListener('input', () => {
    fetch('fetch_assignments.php?q=' + search.value)
        .then(res => res.text())
        .then(data => document.getElementById('results').innerHTML = data);
});
// Initial load
search.dispatchEvent(new Event('input'));
</script>

<?php include 'footer.php'; ?>
