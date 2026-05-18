<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">ResearchHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="papers.php">Papers</a></li>
                <li class="nav-item"><a class="nav-link" href="questions.php">Q&A</a></li>
                <li class="nav-item"><a class="nav-link" href="upload-paper.php">Upload Paper</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php?id=<?= $_SESSION['user_id'] ?>">My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="chatbot.php">🤖 AI Chat</a></li>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>