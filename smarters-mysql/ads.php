<?php
include('includes/header.php');

// Ensure the database is initialized
initializeDatabase($db);

// Handle form submission for adding new URLs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_url'])) {
    $url = sanitize($_POST['url']);
    $stmt = $db->prepare("INSERT INTO ads2_images (ads2_id, url) VALUES (1, ?)");
    $stmt->bind_param('?', $url, );
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>URL added successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to add URL: " . $db->error . "</div>";
    }
}

// Handle deletion of URLs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_url'])) {
    $id = (int)$_POST['id'];
    $stmt = $db->prepare("DELETE FROM ads2_images WHERE id = ?");
    $stmt->bind_param('?', $id, );
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>URL deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to delete URL: " . $db->error . "</div>";
    }
}

// Handle form submission for updating ads table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ads'])) {
    $text1 = sanitize($_POST['text1']);

    // Update ads table
    $stmt1 = $db->prepare("INSERT IGNORE INTO ads (id, text) VALUES (1, ?)");
    $stmt1->bind_param('?', $text1, );
    if ($stmt1->execute()) {
        echo "<div class='alert alert-success'>Ads table updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update ads table: " . $db->error . "</div>";
    }
}

// Handle form submission for updating ads2 table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ads2'])) {
    $text2 = sanitize($_POST['text2']);

    // Update ads2 table
    $stmt2 = $db->prepare("INSERT IGNORE INTO ads2 (id, text) VALUES (1, ?)");
    $stmt2->bind_param('?', $text2, );
    if ($stmt2->execute()) {
        echo "<div class='alert alert-success'>Ads2 table updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update ads2 table: " . $db->error . "</div>";
    }
}

// Handle form submission for updating TMDB API state
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_tmdb'])) {
    $tmdb_api_enabled = (int)$_POST['tmdb_api_enabled'];
    $stmt3 = $db->prepare("UPDATE settings SET tmdb_api_enabled = :tmdb_api_enabled WHERE id = 1");
    $stmt3->bind_param(':tmdb_api_enabled', $tmdb_api_enabled, );
    if ($stmt3->execute()) {
        echo "<div class='alert alert-success'>TMDB API state updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update TMDB API state: " . $db->error . "</div>";
    }
}

// Fetch existing data for display in the form
$res1 = $db->query("SELECT * FROM ads WHERE id=1");
$rowU1 = $res1 ? $res1->fetch_assoc() : ['text' => ''];

$res2 = $db->query("SELECT * FROM ads2 WHERE id=1");
$rowU2 = $res2 ? $res2->fetch_assoc() : ['text' => ''];

$res3 = $db->query("SELECT * FROM ads2_images WHERE ads2_id=1");
$ads2Urls = [];
while ($row = $res3->fetch_assoc()) {
    $ads2Urls[] = $row;
}

// Ensure TMDB API state is initialized
$tmdbApiState = $db->query("SELECT tmdb_api_enabled FROM settings WHERE id=1");
if ($tmdbApiState === null) {
    $db->query("INSERT INTO settings (id, tmdb_api_enabled) VALUES (1, 1)");
    $tmdbApiState = 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ads Management</title>

    <style>
        .poster-card {
            margin: 10px 0;
            text-align: center;
        }
        .poster-card img {
            max-width: 100%;
            max-height: 300px;
            object-fit: cover;
        }
        .poster-card .card-body {
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-header text-center">
                        <h2><i class="icon icon-bullhorn"></i> Ads Management</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" class="mb-4">
                            <h4>Top Text Details</h4>
                            <div class="form-group">
                                <input class="form-control" name="text1" type="text" value="<?= htmlspecialchars($rowU1['text']) ?>" required/>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-info" name="update_ads" type="submit">
                                    <i class="icon icon-check"></i> Update Text
                                </button>
                            </div>
                        </form>

                        <form method="post" class="mb-4">
                            <h4>Ads Text Details</h4>
                            <div class="form-group">
                                <input class="form-control" name="text2" type="text" value="<?= htmlspecialchars($rowU2['text']) ?>" required/>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-info" name="update_ads2" type="submit">
                                    <i class="icon icon-check"></i> Update Ads Text
                                </button>
                            </div>
                        </form>

                        <form method="post" class="mb-4">
                            <h4>Manage Images</h4>
                            <div class="form-group">
                                <label class="form-label" for="url">Add New URL</label>
                                <input class="form-control" name="url" type="text" placeholder="Enter URL" required/>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-info" name="add_url" type="submit">
                                    <i class="icon icon-check"></i> Add URL
                                </button>
                            </div>
                        </form>

                        <form method="post" class="mb-4">
                            <h3>TMDB API Function</h3>
                            <div class="form-group">
                                <label class="form-label" for="tmdb_api_enabled">Enable TMDB API</label>
                                <select class="form-control" name="tmdb_api_enabled">
                                    <option value="1" <?= $tmdbApiState ? 'selected' : '' ?>>Enabled</option>
                                    <option value="0" <?= !$tmdbApiState ? 'selected' : '' ?>>Disabled</option>
                                </select>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-info" name="toggle_tmdb" type="submit">
                                    <i class="icon icon-check"></i> Update TMDB API State
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?php if (count($ads2Urls) > 0): ?>
                    <div class="row">
                        <?php foreach ($ads2Urls as $url): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card poster-card">
                                    <img src="<?= htmlspecialchars($url['url']) ?>" alt="Image">
                                    <div class="card-body">
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $url['id'] ?>">
                                            <button class="btn btn-danger" name="delete_url" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('includes/footer.php'); ?>
