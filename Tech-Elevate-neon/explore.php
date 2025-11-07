<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$projects = $conn->query("
    SELECT p.*, u.name AS uploader 
    FROM projects p 
    JOIN users u ON u.id = p.user_id 
    WHERE p.status='active' 
    ORDER BY p.uploaded_at DESC
");

include 'includes/header.php';
?>

<div class="page-body">
    <div class="explore-header">
        <h1 class="page-title">Explore Projects</h1>
        <div class="search-and-filter">
            <input type="text" id="searchInput" placeholder="Search projects..." class="search-input">
            <div class="filter-dropdown">
                <button class="filter-btn">Filter by <span class="arrow-down"></span></button>
                <div class="filter-content">
                    <a href="#" data-filter="all">All</a>
                    <a href="#" data-filter="web">Web Development</a>
                    <a href="#" data-filter="mobile">Mobile Development</a>
                    <a href="#" data-filter="design">Design</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-case" id="projectsGrid">
        <?php if ($projects->num_rows > 0) : ?>
            <?php while ($p = $projects->fetch_assoc()) : ?>
                <a href="project_view.php?id=<?php echo $p['id']; ?>" class="project-card" data-category="<?php echo strtolower(htmlspecialchars($p['category'])); ?>">
                    <?php
                    $files = explode(',', $p['filename']);
                    $firstFile = trim($files[0]);
                    $ext = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                    ?>

                    <?php if ($firstFile && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $firstFile)) : ?>
                        <img src="uploads/<?php echo htmlspecialchars($firstFile); ?>" alt="thumb" class="project-card-img">
                    <?php else : ?>
                        <div class="project-card-file-blob">
                            <?php echo strtoupper($ext ?: 'FILE'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="project-card-body">
                        <h4 class="project-card-title"><?php echo htmlspecialchars($p['project_title']); ?></h4>
                        <p class="project-card-uploader">by <?php echo htmlspecialchars($p['uploader']); ?></p>
                        <small class="project-card-date"><?php echo htmlspecialchars($p['uploaded_at']); ?></small>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="no-projects-message">
                <p>No projects found at the moment. Check back later!</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="back-to-dash">
        <a class="btn-outline" href="dashboard.php">Back to dashboard</a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const projectsGrid = document.getElementById('projectsGrid');
    const projectCards = projectsGrid.getElementsByClassName('project-card');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        for (let i = 0; i < projectCards.length; i++) {
            const title = projectCards[i].getElementsByClassName('project-card-title')[0];
            if (title.innerHTML.toLowerCase().indexOf(filter) > -1) {
                projectCards[i].style.display = "";
            } else {
                projectCards[i].style.display = "none";
            }
        }
    });

    const filterLinks = document.querySelectorAll('.filter-content a');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            for (let i = 0; i < projectCards.length; i++) {
                if (filter === 'all' || projectCards[i].dataset.category === filter) {
                    projectCards[i].style.display = "";
                } else {
                    projectCards[i].style.display = "none";
                }
            }
        });
    });
});
</script>



<?php include 'includes/footer.php'; ?>